"use client";

import { useState, type ChangeEvent, type FormEvent, type ReactNode } from "react";
import { useLocale, useTranslations } from "next-intl";
import { CheckCircle2, Loader2 } from "lucide-react";
import { API_BASE_URL } from "@/lib/api";
import { buttonClasses } from "@/components/ui/Button";

type Status = "idle" | "sending" | "success" | "error";

const inputClass =
  "h-11 w-full rounded-xl border border-teal-700/15 bg-white/70 px-4 text-sm text-ink placeholder:text-muted focus:border-coral-400 focus:outline-none focus:ring-2 focus:ring-coral-400/40";

const initialValues = {
  name: "",
  email: "",
  phone: "",
  subject: "",
  message: "",
};

type FieldErrors = Partial<Record<keyof typeof initialValues, string>>;

const web3FormsAccessKey = process.env.NEXT_PUBLIC_WEB3FORMS_ACCESS_KEY;

async function sendEmailNotification(values: typeof initialValues) {
  if (!web3FormsAccessKey) return;

  const formData = new FormData();
  formData.append("access_key", web3FormsAccessKey);
  formData.append("from_name", "Dermovive website");
  formData.append("name", values.name);
  formData.append("email", values.email);
  formData.append("phone", values.phone);
  formData.append(
    "subject",
    values.subject || "New Dermovive contact message",
  );
  formData.append("message", values.message);

  const response = await fetch("https://api.web3forms.com/submit", {
    method: "POST",
    body: formData,
  });
  const result = (await response.json()) as { success?: boolean };

  if (!response.ok || !result.success) {
    throw new Error("Web3Forms rejected the notification");
  }
}

export function ContactForm() {
  const t = useTranslations("contact");
  const locale = useLocale();

  const [values, setValues] = useState(initialValues);
  const [status, setStatus] = useState<Status>("idle");
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});

  const set = (key: keyof typeof initialValues) => (
    event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ) => setValues((prev) => ({ ...prev, [key]: event.target.value }));

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setStatus("sending");
    setFieldErrors({});

    try {
      const res = await fetch(`${API_BASE_URL}/contact`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "Accept-Language": locale,
        },
        body: JSON.stringify(values),
      });

      if (res.ok) {
        // The database is the source of truth. Email is a best-effort
        // notification so a temporary provider failure never loses a message.
        try {
          await sendEmailNotification(values);
        } catch {
          // The enquiry is already stored and remains visible in the admin panel.
        }
        setStatus("success");
        setValues(initialValues);
        return;
      }

      if (res.status === 422) {
        const body = (await res.json()) as {
          errors?: Record<string, string[]>;
        };
        const mapped: FieldErrors = {};
        for (const [key, messages] of Object.entries(body.errors ?? {})) {
          mapped[key as keyof typeof initialValues] = messages[0];
        }
        setFieldErrors(mapped);
      }
      setStatus("error");
    } catch {
      setStatus("error");
    }
  }

  if (status === "success") {
    return (
      <div className="flex flex-col items-center justify-center rounded-card border border-teal-700/10 bg-white/60 p-10 text-center">
        <span className="grid h-14 w-14 place-items-center rounded-full bg-coral-100 text-coral-600">
          <CheckCircle2 className="h-7 w-7" />
        </span>
        <h2 className="mt-5 text-xl text-teal-800">{t("form.title")}</h2>
        <p className="mt-2 max-w-sm text-sm leading-relaxed text-muted">
          {t("form.success")}
        </p>
        <button
          type="button"
          onClick={() => setStatus("idle")}
          className="mt-6 text-sm font-medium text-coral-600 hover:text-coral-700"
        >
          {t("form.sendAnother")}
        </button>
      </div>
    );
  }

  const sending = status === "sending";

  return (
    <div className="rounded-card border border-teal-700/10 bg-white/60 p-6 sm:p-8">
      <h2 className="text-xl text-teal-800">{t("form.title")}</h2>
      <p className="mt-2 text-sm text-muted">{t("form.note")}</p>

      <form className="mt-6 space-y-4" onSubmit={handleSubmit} noValidate>
        <Field id="name" label={t("form.name")} error={fieldErrors.name}>
          <input
            id="name"
            name="name"
            type="text"
            required
            value={values.name}
            onChange={set("name")}
            className={inputClass}
          />
        </Field>

        <Field id="email" label={t("form.email")} error={fieldErrors.email}>
          <input
            id="email"
            name="email"
            type="email"
            required
            value={values.email}
            onChange={set("email")}
            className={inputClass}
          />
        </Field>

        <Field id="phone" label={t("form.phone")} error={fieldErrors.phone}>
          <input
            id="phone"
            name="phone"
            type="tel"
            dir="ltr"
            value={values.phone}
            onChange={set("phone")}
            className={`${inputClass} rtl:text-right`}
          />
        </Field>

        <Field id="subject" label={t("form.subject")} error={fieldErrors.subject}>
          <input
            id="subject"
            name="subject"
            type="text"
            value={values.subject}
            onChange={set("subject")}
            className={inputClass}
          />
        </Field>

        <Field id="message" label={t("form.message")} error={fieldErrors.message}>
          <textarea
            id="message"
            name="message"
            rows={5}
            required
            value={values.message}
            onChange={set("message")}
            className="w-full rounded-xl border border-teal-700/15 bg-white/70 px-4 py-3 text-sm text-ink placeholder:text-muted focus:border-coral-400 focus:outline-none focus:ring-2 focus:ring-coral-400/40"
          />
        </Field>

        {status === "error" && (
          <p className="text-sm text-coral-600" role="alert">
            {t("form.error")}
          </p>
        )}

        <button
          type="submit"
          disabled={sending}
          className={buttonClasses({
            size: "lg",
            className: "w-full justify-center disabled:opacity-70",
          })}
        >
          {sending && <Loader2 className="h-4 w-4 animate-spin" />}
          {sending ? t("form.sending") : t("form.submit")}
        </button>
      </form>
    </div>
  );
}

function Field({
  id,
  label,
  error,
  children,
}: {
  id: string;
  label: string;
  error?: string;
  children: ReactNode;
}) {
  return (
    <div>
      <label
        htmlFor={id}
        className="mb-1.5 block text-sm font-medium text-teal-800"
      >
        {label}
      </label>
      {children}
      {error && (
        <p className="mt-1 text-xs text-coral-600" role="alert">
          {error}
        </p>
      )}
    </div>
  );
}

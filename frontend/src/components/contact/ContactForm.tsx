"use client";

import { useState, type FormEvent } from "react";

type ContactFormCopy = {
  name: string;
  email: string;
  subject: string;
  message: string;
  submit: string;
  sending: string;
  success: string;
  error: string;
  unavailable: string;
};

type SubmissionStatus = "idle" | "sending" | "success" | "error";

export function ContactForm({
  accessKey,
  copy,
}: {
  accessKey?: string;
  copy: ContactFormCopy;
}) {
  const [status, setStatus] = useState<SubmissionStatus>("idle");
  const isConfigured = Boolean(accessKey);
  const isSending = status === "sending";

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();

    if (!accessKey || isSending) return;

    const form = event.currentTarget;
    const formData = new FormData(form);
    formData.append("access_key", accessKey);
    formData.append("from_name", "Dermovive website");

    setStatus("sending");

    try {
      const response = await fetch("https://api.web3forms.com/submit", {
        method: "POST",
        body: formData,
      });
      const result = (await response.json()) as { success?: boolean };

      if (!response.ok || !result.success) {
        throw new Error("Web3Forms rejected the submission");
      }

      form.reset();
      setStatus("success");
    } catch {
      setStatus("error");
    }
  }

  const fields = ["name", "email", "subject"] as const;

  return (
    <form
      className="mt-6 space-y-4"
      aria-label={copy.submit}
      onSubmit={handleSubmit}
    >
      {fields.map((field) => (
        <div key={field}>
          <label
            htmlFor={`contact-${field}`}
            className="mb-1.5 block text-sm font-medium text-teal-800"
          >
            {copy[field]}
          </label>
          <input
            id={`contact-${field}`}
            name={field}
            type={field === "email" ? "email" : "text"}
            required
            autoComplete={field === "name" ? "name" : field === "email" ? "email" : undefined}
            disabled={!isConfigured || isSending}
            className="h-11 w-full rounded-xl border border-teal-700/15 bg-white/70 px-4 text-sm text-ink outline-none transition focus:border-coral-500 focus:ring-2 focus:ring-coral-200 disabled:cursor-not-allowed disabled:opacity-60"
          />
        </div>
      ))}

      <div>
        <label
          htmlFor="contact-message"
          className="mb-1.5 block text-sm font-medium text-teal-800"
        >
          {copy.message}
        </label>
        <textarea
          id="contact-message"
          name="message"
          rows={5}
          required
          disabled={!isConfigured || isSending}
          className="w-full rounded-xl border border-teal-700/15 bg-white/70 px-4 py-3 text-sm text-ink outline-none transition focus:border-coral-500 focus:ring-2 focus:ring-coral-200 disabled:cursor-not-allowed disabled:opacity-60"
        />
      </div>

      <input
        type="checkbox"
        name="botcheck"
        className="hidden"
        tabIndex={-1}
        autoComplete="off"
      />

      <button
        type="submit"
        disabled={!isConfigured || isSending}
        className="h-11 w-full rounded-pill bg-coral-500 px-6 text-sm font-medium text-white transition hover:bg-coral-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-coral-400 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
      >
        {isSending ? copy.sending : copy.submit}
      </button>

      <p
        aria-live="polite"
        className={`min-h-5 text-sm ${
          status === "error" || !isConfigured
            ? "text-coral-700"
            : "text-teal-700"
        }`}
      >
        {!isConfigured
          ? copy.unavailable
          : status === "success"
            ? copy.success
            : status === "error"
              ? copy.error
              : ""}
      </p>
    </form>
  );
}

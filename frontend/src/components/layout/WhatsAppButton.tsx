import { useTranslations } from "next-intl";

type WhatsAppButtonProps = {
  phone: string;
};

function WhatsAppIcon({ className }: { className?: string }) {
  return (
    <svg
      aria-hidden="true"
      className={className}
      viewBox="0 0 32 32"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M27.2 4.7A15 15 0 0 0 3.6 22.8L1.5 30.5l7.9-2.1A15 15 0 0 0 27.2 4.7Z"
        fill="currentColor"
      />
      <path
        d="M11.5 8.4c-.3-.7-.7-.7-1.1-.7h-.9c-.3 0-.8.1-1.2.6-.4.5-1.6 1.6-1.6 3.9s1.7 4.5 1.9 4.8c.2.3 3.3 5.1 8.1 6.9 4 1.6 4.8 1.3 5.7 1.2.9-.1 2.8-1.1 3.2-2.2.4-1.1.4-2 .3-2.2-.1-.2-.4-.3-.9-.6-.5-.2-2.8-1.4-3.2-1.5-.4-.2-.8-.2-1.1.2-.3.5-1.2 1.5-1.5 1.8-.3.3-.6.4-1.1.1-.5-.2-2-.7-3.8-2.3-1.4-1.2-2.3-2.8-2.6-3.2-.3-.5 0-.7.2-1 .2-.2.5-.6.7-.8.2-.3.3-.5.5-.8.2-.3.1-.6 0-.8-.1-.2-1.1-2.5-1.5-3.4Z"
        fill="#25D366"
      />
    </svg>
  );
}

export function WhatsAppButton({ phone }: WhatsAppButtonProps) {
  const t = useTranslations("whatsapp");
  const number = phone.replace(/\D/g, "");

  if (!number) return null;

  const href = `https://wa.me/${number}?text=${encodeURIComponent(t("message"))}`;

  return (
    <div
      className="group fixed end-5 z-50 sm:end-7"
      style={{ bottom: "calc(1.25rem + env(safe-area-inset-bottom))" }}
    >
      <span
        role="tooltip"
        className="pointer-events-none absolute end-full top-1/2 me-3 hidden -translate-y-1/2 whitespace-nowrap rounded-pill bg-teal-900 px-3.5 py-2 text-xs font-semibold text-cream opacity-0 shadow-soft transition-all duration-300 group-hover:-translate-x-1 group-hover:opacity-100 group-focus-within:-translate-x-1 group-focus-within:opacity-100 sm:block rtl:group-hover:translate-x-1 rtl:group-focus-within:translate-x-1"
      >
        {t("label")}
      </span>

      <span
        aria-hidden="true"
        className="whatsapp-pulse absolute inset-1 rounded-full"
      />

      <a
        href={href}
        target="_blank"
        rel="noreferrer noopener"
        aria-label={t("ariaLabel")}
        className="relative grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-[0_12px_32px_rgba(22,163,74,0.38)] ring-4 ring-cream/90 transition duration-300 ease-[var(--ease-soft)] hover:-translate-y-1 hover:scale-105 hover:bg-[#20bd5a] hover:shadow-[0_16px_38px_rgba(22,163,74,0.48)] focus-visible:outline-none focus-visible:ring-[5px] focus-visible:ring-coral-300 sm:h-16 sm:w-16"
      >
        <WhatsAppIcon className="h-8 w-8 sm:h-9 sm:w-9" />
      </a>
    </div>
  );
}

import Image from "next/image";
import { useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { cn } from "@/lib/utils";

export function Logo({ className }: { className?: string }) {
  const t = useTranslations("common");
  return (
    <Link
      href="/"
      aria-label={t("brand")}
      className={cn("inline-flex items-center", className)}
    >
      <Image
        src="/logo.png"
        alt={t("brand")}
        width={1571}
        height={771}
        priority
        className="h-9 w-auto sm:h-10"
      />
    </Link>
  );
}

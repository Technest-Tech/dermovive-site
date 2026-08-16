export const CONTACT_EMAIL = "dermovivepharmasn@gmail.com";
export const CONTACT_PHONE = "+221 77 486 22 47";
export const EGYPT_PHONE = "01002058424";
export const CONTACT_ADDRESS = "مدينة نصر، القاهرة";
export const FACEBOOK_URL =
  "https://www.facebook.com/people/Dermovive-pharma/61578885461744/";
export const TIKTOK_URL = "https://www.tiktok.com/@dermovive_pharma";

export function digitsOnlyPhone(phone: string): string {
  return phone.replace(/\D/g, "");
}

export function egyptPhoneHref(phone: string): string {
  const digits = digitsOnlyPhone(phone);
  if (digits.startsWith("0")) return `+20${digits.slice(1)}`;
  if (digits.startsWith("20")) return `+${digits}`;
  return `+${digits}`;
}

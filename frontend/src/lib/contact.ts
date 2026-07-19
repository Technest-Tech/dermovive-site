export const CONTACT_EMAIL = "dermovivepharmasn@gmail.com";
export const CONTACT_PHONE = "+221 77 486 22 47";
export const FACEBOOK_URL =
  "https://www.facebook.com/people/Dermovive-pharma/61578885461744/";
export const TIKTOK_URL = "https://www.tiktok.com/@dermovive_pharma";

export function digitsOnlyPhone(phone: string): string {
  return phone.replace(/\D/g, "");
}

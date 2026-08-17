import type { Metadata } from "next";
import { CmsPage, cmsPageMetadata } from "@/components/content/CmsPage";

type Params = Promise<{ locale: string }>;
const SLUG = "refund-policy";

export function generateMetadata({
  params,
}: {
  params: Params;
}): Promise<Metadata> {
  return cmsPageMetadata(SLUG, params);
}

export default function RefundPolicyPage({ params }: { params: Params }) {
  return <CmsPage slug={SLUG} params={params} />;
}

import type { NextConfig } from "next";
import createNextIntlPlugin from "next-intl/plugin";

const withNextIntl = createNextIntlPlugin();

type RemotePatterns = NonNullable<
  NonNullable<NextConfig["images"]>["remotePatterns"]
>;

// Local Laravel media (dev) plus the configured API origin (prod media host).
const remotePatterns: RemotePatterns = [
  { protocol: "http", hostname: "127.0.0.1", port: "8000", pathname: "/**" },
  { protocol: "http", hostname: "localhost", port: "8000", pathname: "/**" },
];

if (process.env.NEXT_PUBLIC_API_URL) {
  try {
    const url = new URL(process.env.NEXT_PUBLIC_API_URL);
    remotePatterns.push({
      protocol: url.protocol === "https:" ? "https" : "http",
      hostname: url.hostname,
      port: url.port,
      pathname: "/**",
    });
  } catch {
    // Malformed URL — fall back to the local patterns above.
  }
}

const nextConfig: NextConfig = {
  // Slim, self-contained server output for containerized/Node deploys.
  output: "standalone",
  images: {
    formats: ["image/avif", "image/webp"],
    remotePatterns,
    dangerouslyAllowLocalIP: process.env.NODE_ENV !== "production",
  },
};

export default withNextIntl(nextConfig);

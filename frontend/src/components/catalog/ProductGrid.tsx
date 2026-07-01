import { ProductCard } from "@/components/product/ProductCard";
import type { ProductCard as ProductCardType } from "@/lib/types";

/** Responsive grid of product cards, shared by category pages and the listing. */
export function ProductGrid({ products }: { products: ProductCardType[] }) {
  return (
    <div className="grid grid-cols-2 gap-x-5 gap-y-9 sm:grid-cols-3 lg:grid-cols-4">
      {products.map((product) => (
        <ProductCard key={product.id} product={product} />
      ))}
    </div>
  );
}

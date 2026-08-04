export interface Category {
  id: number;
  name: string;
  slug: string;
  order: number;
  is_active: boolean;
  image: string | null;
}

export interface Banner {
  id: number;
  title: string | null;
  subtitle: string | null;
  button_text: string | null;
  button_url: string | null;
  order: number;
  is_active: boolean;
  image: string | null;
}

export interface ProductImage {
  id: number;
  thumb: string;
  large: string;
}

export type Availability = "in_stock" | "out_of_stock" | "on_request";

export interface Brand {
  id: number;
  name: string;
  slug: string;
  is_active: boolean;
}

export interface Product {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  features: string[] | null;
  price: string;
  sale_price: string | null;
  availability: Availability;
  is_featured: boolean;
  is_active: boolean;
  category: Category;
  brand: Brand | null;
  images: ProductImage[];
}

export interface StoreSettings {
  store_name: string;
  whatsapp_number: string;
  email: string | null;
  facebook_url: string | null;
  instagram_url: string | null;
  tiktok_url: string | null;
  address: string | null;
  schedule: string | null;
  privacy_policy: string | null;
  terms_conditions: string | null;
  primary_color: string;
  secondary_color: string;
  logo: string | null;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: { current_page: number; last_page: number; total: number };
}

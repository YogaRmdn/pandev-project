export interface Portfolio {
  id: string;
  thumbnail: string | null;
  name: string;
  category: string;
  description: string;
  demo_link: string | null;
  repository_link: string;
  status: string;
  tech_stacks?: string[] | unknown;
  created_at: Date;
  updated_at: Date;
  galery?: {
    id: string;
    portfolio_id: string;
    image_url: string;
    created_at: Date;
  }[];
}

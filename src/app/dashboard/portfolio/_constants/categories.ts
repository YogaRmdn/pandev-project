export const PORTFOLIO_CATEGORIES = [
  "Web App",
  "Mobile App",
  "Desktop App",
  "Multiplatform App",
  "Cyber Security Tools",
  "Design & Multimedia",
  "IoT Solutions",
  "Data & GIS",
] as const;

export type PortfolioCategory = (typeof PORTFOLIO_CATEGORIES)[number];

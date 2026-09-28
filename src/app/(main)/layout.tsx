import Navbar from "@/components/ui/navbar";
import { Toaster } from "@/components/ui/sonner";
import Footer from "./_components/footer";

export default function HomeLayout({ children }: LayoutProps<"/">) {
  return (
    <>
      <Toaster />
      <Navbar />
      {children}
      <Footer />
    </>
  );
}

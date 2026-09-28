import { Marquee } from "@/components/ui/marquee";
import { cn } from "@/lib/utils";

export default function InfiniteScrollMarquee() {
  return (
    <div className="flex flex-col gap-8 absolute top-40 left-0 right-0 -z-10">
      <div className="flex items-center justify-center w-full ">
        <Marquee pauseOnHover={false}>
          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8",
            )}
          >
            Web Application
          </span>
          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8 text-primary",
            )}
          >
            Mobile Application
          </span>
          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8",
            )}
          >
            Desktop Application
          </span>
        </Marquee>
      </div>
      <div className="flex items-center justify-center w-full ">
        <Marquee pauseOnHover={false}>
          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8 text-primary",
            )}
          >
            Cyber Security
          </span>

          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8",
            )}
          >
            IoT Projects
          </span>
          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8 text-primary",
            )}
          >
            Web Pentest Tools
          </span>
        </Marquee>
      </div>
      <div className="flex items-center justify-center w-full ">
        <Marquee pauseOnHover={false}>
          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8",
            )}
          >
           Data & GIS
          </span>
          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8 text-primary",
            )}
          >
            Video & Photo Editing
          </span>
          <span
            className={cn(
              "shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl mx-8",
            )}
          >
            Multimedia
          </span>
        </Marquee>
      </div>
    </div>
  );
}

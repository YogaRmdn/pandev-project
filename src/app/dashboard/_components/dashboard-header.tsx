import { Separator } from "@/components/ui/separator";
import { SidebarTrigger } from "@/components/ui/sidebar";

interface PropTypes {
    title: string;
    description?: string;
}

export default function DashboardHeader(props: PropTypes) {
    const { title, description } = props
    return (
        <header className="flex h-16 shrink-0 items-center gap-2 px-4 w-full  border-b">
            <SidebarTrigger className="inline" />
            <Separator orientation="vertical" className="mr-2 data-[orientation=vertical]:h-14" />
            <div>
                <div className="pb-2">
                    <div className="text-xl font-bold">{title}</div>
                    {description && <div>{description}</div>}
                </div>
            </div>
        </header>
    )
}

"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar";
import Image from "next/image";
import { NavUser } from "./nav-user";
import NavMain from "./nav-main";
import { NavSecondary } from "./nav-secondary";
import { NavAdmin } from "./nav-admin";
import { SIDEBAR_MAIN_ITEMS, SIDEBAR_SECONDARY_ITEMS, SIDEBAR_ADMIN_ITEMS } from "../_constants/sidebar-items";
import { useSession } from "@/lib/auth-client";

export default function DashboardSidebar() {
  const pathname = usePathname();
  const { data: session } = useSession();
  const activePath = "/" + pathname.split("/").filter(Boolean).slice(0, 2).join("/");

  return (
    <Sidebar collapsible="icon">
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton size="lg" asChild>
              <Link href="/dashboard">
                <div className="flex aspect-square size-8 items-center justify-center rounded-lg text-primary-foreground">
                  <Image width={30} height={30} src={"/assets/common/logo.png"} alt="PanDev Logo" />
                </div>
                <div className="grid flex-1 text-left text-sm leading-tight">
                  <span className="truncate font-semibold">PanDev</span>
                </div>
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>
      <SidebarContent>
        <NavMain items={SIDEBAR_MAIN_ITEMS} activePath={activePath} />
        {(session?.user as { role?: string })?.role === "ADMIN" && (
          <NavAdmin items={SIDEBAR_ADMIN_ITEMS} activePath={activePath} />
        )}
        <NavSecondary items={SIDEBAR_SECONDARY_ITEMS} activePath={activePath} />
      </SidebarContent>
      <SidebarFooter>
        <NavUser />
      </SidebarFooter>
    </Sidebar>
  );
}

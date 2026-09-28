"use client"


import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar"
import { LucideProps } from "lucide-react"
import Link from "next/link";
import { ComponentPropsWithoutRef, ForwardRefExoticComponent, RefAttributes } from "react"

export function NavSecondary({
    items,
    activePath,
    ...props
}: {
    items: {
        title: string
        href: string
        icon: ForwardRefExoticComponent<Omit<LucideProps, "ref"> & RefAttributes<SVGSVGElement>>
    }[];
    activePath: string
} & ComponentPropsWithoutRef<typeof SidebarGroup>) {
    return (
        <SidebarGroup {...props}>
            <SidebarGroupLabel>Akun</SidebarGroupLabel>
            <SidebarGroupContent>
                <SidebarMenu>
                    {items.map((item) => (
                        <SidebarMenuItem key={item.href}>
                            <SidebarMenuButton
                                asChild
                                isActive={activePath === item.href}
                                tooltip={item.title}
                                className="h-12 rounded-lg data-active:bg-primary data-active:hover:bg-primary/90 data-active:text-white data-active:hover:text-white"
                            >
                                <Link href={item.href}>
                                    <item.icon />
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ))}
                </SidebarMenu>
            </SidebarGroupContent>
        </SidebarGroup>
    )
}

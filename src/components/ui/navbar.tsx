"use client"

import Link from "next/link"
import { usePathname } from "next/navigation"

import { Button } from "@/components/ui/button"
import {
  Sheet,
  SheetClose,
  SheetContent,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet"
import { cn } from "@/lib/utils"
import { Grid3x3Icon, LogOutIcon, MenuIcon, UserIcon, ZapIcon } from "lucide-react"
import Image from "next/image"
import { signOut, useSession } from "@/lib/auth-client"
import { Popover, PopoverContent, PopoverTrigger } from "./popover"

const NAV_ITEMS = [
  { href: "/portfolio", label: "Portofolio" },
  { href: "/tentang", label: "Tentang" },
  { href: "/kontak", label: "Kontak" },
] as const

function isActive(pathname: string, href: string) {
  return pathname === href
}

export default function Navbar() {
  const pathname = usePathname()

  const session = useSession()


  const handleLogout = async () => {
    await signOut()
    session.refetch()
  }

  const handleLoginBtn = () => {
    if (session.data) {
      return (
        <div className="ml-4">
          <Popover>
            <PopoverTrigger asChild>
              <Button size={"icon"} variant={"ghost"}><UserIcon /></Button>
            </PopoverTrigger>
            <PopoverContent className="flex flex-col items-start w-fit">
              <Link href={"/dashboard"}>
                <Button variant={"outline"}><Grid3x3Icon /> Dashboard</Button>
              </Link>
              <Button variant={"outline"} onClick={() => handleLogout()}>
                <LogOutIcon />
                Logout
              </Button>
            </PopoverContent>
          </Popover>
        </div>
      )
    } else {
      return (
        <Link className="ml-4" href={"/auth/login"}>
          <Button>Login</Button>
        </Link>
      )
    }
  }


  return (
    <header className="sticky top-0 z-50 w-full border-b bg-background/80 backdrop-blur-md supports-backdrop-filter:bg-background/60">
      <div className="mx-auto flex h-16 max-w-screen-2xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <Link
          href="/"
          className="flex items-center gap-2 font-heading text-lg font-semibold tracking-tight"
          aria-label="PanDev - Beranda"
        >
          <span className="inline-flex size-6 items-center justify-center rounded-md text-primary-foreground">
            <Image src={"/assets/common/logo.png"} width={50} height={50} alt="PanDev Logo" />
          </span>
          PANDEV
        </Link>

        <nav
          className="hidden items-center md:flex"
          aria-label="Navigasi utama"
        >
          {NAV_ITEMS.map((item) => {
            const active = isActive(pathname, item.href)
            console.log(pathname)
            return (
              <Link
                key={item.href}
                href={item.href}
                aria-current={active ? "page" : undefined}
                className={cn("relative rounded-md px-3 py-2 text-sm font-medium ", active ? "text-primary" : "text-foreground")}

              >
                {item.label}
              </Link>
            )
          })}
          {handleLoginBtn()}
        </nav>

        <Sheet>
          <SheetTrigger asChild>
            <Button
              variant="ghost"
              size="icon"
              className="md:hidden"
              aria-label="Buka menu navigasi"
            >
              <MenuIcon />
            </Button>
          </SheetTrigger>
          <SheetContent side="right" className="w-3/4 sm:max-w-sm">
            <SheetTitle className="font-heading text-lg font-semibold tracking-tight">
              PANDEV
            </SheetTitle>
            <nav
              className="flex flex-col gap-1"
              aria-label="Navigasi utama"
            >
              {NAV_ITEMS.map((item) => {
                const active = isActive(pathname, item.href)

                return (
                  <SheetClose key={item.href} asChild>
                    <Link
                      href={item.href}
                      aria-current={active ? "page" : undefined}
                      className={cn(
                        "rounded-md px-3 py-2.5 text-sm font-medium transition-colors",
                        active
                          ? "bg-muted text-foreground"
                          : "text-muted-foreground hover:bg-muted hover:text-foreground"
                      )}
                    >
                      {item.label}
                    </Link>
                  </SheetClose>
                )
              })}
            </nav>
          </SheetContent>
        </Sheet>
      </div>
    </header>
  )
}
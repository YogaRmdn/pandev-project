
import { LoginForm } from "@/components/login-form"
import { Metadata } from "next"
import Image from "next/image"

export const metadata: Metadata = {
  title: "PanDev | Login",
}

export default function LoginPage() {
  return (
    <div className="grid min-h-svh lg:grid-cols-2">
      <div className="flex flex-col gap-4 p-6 md:p-10">
        <div className="flex justify-center gap-2 md:justify-start">
          <a href="#" className="flex items-center gap-2 font-medium">
            <div className="flex size-6 items-center justify-center rounded-md text-primary-foreground">
              <Image width={30} height={30} src={"/assets/common/logo.png"} alt="PanDev Logo" />
            </div>
            PanDev
          </a>
        </div>
        <div className="flex flex-1 items-center justify-center">
          <div className="w-full max-w-xs"> 
            <LoginForm />
          </div>
        </div>
      </div>
      <div className="hidden bg-muted lg:flex lg:flex-col justify-center items-center">
        <Image
          width={200}
          height={200}
          src="/assets/common/logo.png"
          alt="Image"
          className="inset-0 object-contain dark:brightness-[0.2] dark:grayscale"
        />
        <div className="text-2xl font-bold text-primary">Unlock What's Possible</div>
        <div className="italic">Digital solutions, where ideas becomes reality</div>
      </div>
    </div>
  )
}

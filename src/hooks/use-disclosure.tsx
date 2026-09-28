import { useState } from "react"

export const useDisclosure = () => {
    const [open, setOpen] = useState(false)
    return {open, setOpen}
}
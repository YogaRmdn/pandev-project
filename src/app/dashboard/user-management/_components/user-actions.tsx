"use client";

import { Button } from "@/components/ui/button";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { MoreHorizontalIcon, PencilIcon, TrashIcon } from "lucide-react";

interface PropTypes {
  userId: string;
}

export default function UserActions({ userId }: PropTypes) {
  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button variant="ghost" size="icon">
          <MoreHorizontalIcon className="size-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent align="end" className="w-40">
        <div className="flex flex-col gap-1">
          <Button
            variant="ghost"
            className="justify-start gap-2"
            size="sm"
          >
            <PencilIcon className="size-4" />
            Edit
          </Button>
          <Button
            variant="ghost"
            className="justify-start gap-2 text-destructive hover:text-destructive"
            size="sm"
          >
            <TrashIcon className="size-4" />
            Hapus
          </Button>
        </div>
      </PopoverContent>
    </Popover>
  );
}

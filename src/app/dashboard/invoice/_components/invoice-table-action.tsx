import { Button } from "@/components/ui/button";
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from "@/components/ui/tooltip";
import { cn } from "@/lib/utils";
import { ReactNode } from "react";

interface PropTypes {
  label: string;
  icon: ReactNode;
  tooltipContent?: string;
  isDestructive?: boolean;
  handleClick: () => void;
}

export default function InvoiceTableAction(props: PropTypes) {
  const { label, icon, tooltipContent, handleClick, isDestructive } = props;
  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <Button
          variant="ghost"
          className={cn(
            "text-muted-foreground w-fit cursor-pointer",
            isDestructive && "hover:text-destructive",
          )}
          onClick={handleClick}
          size={"icon"}
        >
          {icon}
        </Button>
      </TooltipTrigger>
      <TooltipContent>
        <div className="capitalize">{tooltipContent ?? label}</div>
      </TooltipContent>
    </Tooltip>
  );
}

"use client";

import { Dispatch, SetStateAction, useId } from "react";
import {
  Combobox,
  ComboboxChips,
  ComboboxValue,
  ComboboxChipsInput,
  ComboboxContent,
  ComboboxList,
  ComboboxItem,
  ComboboxEmpty,
} from "@/components/ui/combobox";
import { XIcon } from "lucide-react";
import { AVAILABLE_TECH_STACKS } from "@/lib/constants";

interface TechStack {
  id: string;
  value: string;
}

interface PropTypes {
  value: string[];
  onChange: Dispatch<SetStateAction<string[]>>;
}

export default function TechStackSelect(props: PropTypes) {
  const { value, onChange } = props;
  const id = useId();

  const items: TechStack[] = AVAILABLE_TECH_STACKS.filter(
    (tech) => !value.includes(tech),
  ).map((tech) => ({
    id: tech,
    value: tech,
  }));

  const selectedItems: TechStack[] = value.map((tech) => ({
    id: tech,
    value: tech,
  }));

  const handleValueChange = (newValue: TechStack | TechStack[] | null) => {
    if (Array.isArray(newValue)) {
      onChange(newValue.map((item) => item.value));
    } else if (newValue === null) {
      onChange([]);
    }
  };

  return (
    <Combobox
      multiple
      value={selectedItems}
      onValueChange={handleValueChange}
      items={items}
    >
      <ComboboxChips>
        <ComboboxValue>
          {(val: TechStack[]) =>
            val.map((tech) => (
              <span
                key={tech.id}
                className="inline-flex items-center gap-1 rounded-md bg-secondary px-2 py-1 text-xs font-medium text-secondary-foreground"
              >
                {tech.value}
                <button
                  type="button"
                  className="ml-0.5 rounded-full p-0.5 hover:bg-destructive/20"
                  onClick={() => {
                    onChange(value.filter((v) => v !== tech.value));
                  }}
                >
                  <XIcon className="size-3" />
                </button>
              </span>
            ))
          }
        </ComboboxValue>
        <ComboboxChipsInput
          id={id}
          placeholder={value.length === 0 ? "Tambah tech stack..." : ""}
          className="flex-1 bg-transparent outline-none placeholder:text-muted-foreground min-w-[120px]"
        />
      </ComboboxChips>
      <ComboboxContent>
        <ComboboxList>
          {(item: TechStack) => (
            <ComboboxItem key={item.id} value={item}>
              {item.value}
            </ComboboxItem>
          )}
        </ComboboxList>
        <ComboboxEmpty>Tidak ada tech stack ditemukan</ComboboxEmpty>
      </ComboboxContent>
    </Combobox>
  );
}

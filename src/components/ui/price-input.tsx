"use client";

import { ControllerFieldState, ControllerRenderProps } from "react-hook-form";
import { Field, FieldLabel } from "./field";
import { Input } from "./input";
import { ChangeEvent, useId, useState } from "react";
import { convertToIDR } from "@/lib/utils";

interface PropTypes {
  field: ControllerRenderProps<any>;
  fieldState: ControllerFieldState;
}

export default function PriceInput(props: PropTypes) {
  const { field, fieldState } = props;
  const id = useId();

  const handleOnChange = (e: ChangeEvent<HTMLInputElement>) => {
    field.onChange(Number(e.target.value));
  };

  return (
    <Input
      id={`price-input-${id}`}
      type="string"
      aria-invalid={fieldState.invalid}
      placeholder="0"
      value={convertToIDR(field.value)}
      onChange={(e) => handleOnChange(e)}
    />
  );
}

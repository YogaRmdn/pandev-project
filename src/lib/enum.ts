import { Role } from "@prisma/client";

export function toRole(value: string): Role {
  const normalized = value.toUpperCase();

  if (!(normalized in Role)) {
    throw new Error(`Invalid role: "${value}". Valid roles: ${Object.values(Role).join(", ")}`);
  }

  return Role[normalized as keyof typeof Role];
}

export { Role };

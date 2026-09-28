import { Role } from "@prisma/client";

interface IUser {
    id: string;
    fullname: string;
    email: string;
    role: Role;
    image: string;
}


export type {IUser}
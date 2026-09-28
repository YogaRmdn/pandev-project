"use client";

import { getUsers } from "@/app/actions/user-management";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { useQuery } from "@tanstack/react-query";
import { FilterIcon, PlusIcon } from "lucide-react";
import { useState } from "react";
import { Fragment } from "react/jsx-runtime";
import CreateUserDialog from "./create-user-dialog";
import UsersTable from "./users-table";
import { IUser } from "../_types/User";

export default function UserManagementContent() {
  const {
    isPending,
    data: users,
    refetch,
  } = useQuery({
    queryFn: getUsers,
    queryKey: ["users"],
  });
  const [open, setOpen] = useState(false);
  return (
    <Fragment>
      <Card className="w-full gap-2">
        <CardHeader className="flex items-center justify-between border-b">
          <div>
            <div className="flex gap-2">
              <CardTitle>Data User</CardTitle>
              {users && users?.length > 0 ? (
                <Badge className="bg-green-100 text-primary px-1 hover:bg-green-100">
                  Total {users?.length}
                </Badge>
              ) : null}
            </div>
            <CardDescription>Berikut semua data user yang ada</CardDescription>
          </div>
          <div className="space-x-2">
            <Button variant={"outline"}>
              <FilterIcon /> Filter
            </Button>
            <Button className="cursor-pointer" onClick={() => setOpen(true)}>
              <PlusIcon /> Tambah User
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <UsersTable
            isPending={isPending}
            users={users as IUser[]}
            refetch={refetch}
          />
        </CardContent>
      </Card>
      <CreateUserDialog open={open} setOpen={setOpen} refetch={refetch} />
    </Fragment>
  );
}

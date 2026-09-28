"use client";

import { deleteTransaction, getTransactions } from "@/app/actions/transaction";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Spinner } from "@/components/ui/spinner";
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { cn, convertToIDR } from "@/lib/utils";
import { useMutation } from "@tanstack/react-query";
import {
  MinusIcon,
  PencilIcon,
  PlusIcon,
  Trash2Icon,
  UserIcon,
} from "lucide-react";
import { Fragment } from "react/jsx-runtime";
import { toast } from "sonner";
import { useState } from "react";
import { IUser } from "../_types/User";
import { deleteUser } from "@/app/actions/user-management";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import DeleteUserDialog from "./delete-user-dialog";
import EditUserDialog from "./edit-user-dialog";

const TABLE_HEADER = ["#", "Full Name", "Email", "Role", "Action"];

interface PropTypes {
  isPending: boolean;
  users?: IUser[];
  refetch: () => void;
}

export default function UsersTable(props: PropTypes) {
  const { users, isPending, refetch } = props;

  const [selectedUser, setSelectedUser] = useState<IUser | null>(null);
  const [openEditDialog, setOpenEditDialog] = useState(false);
  const [openDeleteDialog, setOpenDeleteDialog] = useState(false);
  const { mutate: mutateDeleteUser, isPending: isPendingDeleteUser } =
    useMutation({
      mutationFn: (id: string) => deleteUser(id),
      onSuccess: () => {
        refetch();
        toast.success("Akun berhasil dihapus");
        setSelectedUser(null);
      },
      onError: () => {
        toast.error("Gagal menghapus transaksi");
      },
    });

  return (
    <Fragment>
      <Table>
        <TableHeader>
          <TableRow>
            {TABLE_HEADER.map((header) => (
              <TableHead key={`th-${header}`}>{header}</TableHead>
            ))}
          </TableRow>
        </TableHeader>
        <TableBody>
          {users?.map((user, index) => {
            return (
              <TableRow key={user.id}>
                <TableCell>{index + 1}</TableCell>
                <TableCell>
                  <div className="flex gap-3 items-center">
                    <Avatar>
                      <AvatarImage
                        src={user.image}
                        alt={`${user.fullname}-image`}
                      />
                      <AvatarFallback>
                        <UserIcon />
                      </AvatarFallback>
                    </Avatar>
                    <span>{user.fullname}</span>
                  </div>
                </TableCell>
                <TableCell>{user.email}</TableCell>
                <TableCell className="capitalize">
                  <Badge
                    variant={user.role === "ADMIN" ? "default" : "secondary"}
                  >
                    {user.role}
                  </Badge>
                </TableCell>
                <TableCell className="flex gap-2">
                  <Button
                    variant="ghost"
                    className="text-muted-foreground w-fit"
                    size={"icon"}
                    onClick={() => {
                      setSelectedUser(user)
                      setOpenEditDialog(true)
                    }}
                  >
                    <PencilIcon className="size-4" />
                  </Button>
                  <Button
                    variant="ghost"
                    className="text-muted-foreground w-fit hover:text-destructive"
                    size={"icon"}
                    onClick={() => {
                      setSelectedUser(user);
                      setOpenDeleteDialog(true);
                    }}
                  >
                    <Trash2Icon className="size-4" />
                  </Button>
                </TableCell>
              </TableRow>
            );
          })}
        </TableBody>
        {isPending && (
          <TableCaption className="mb-4">
            <Spinner variant="circle" />
          </TableCaption>
        )}
        {!isPending && users?.length === 0 && (
          <TableCaption className="mb-4">Belum ada data user</TableCaption>
        )}
      </Table>
      {/* <div className="flex items-center justify-between mt-4">
            <div className="flex items-center gap-2">
              <div className="text-sm text-muted-foreground">Rows per page</div>
              <Select
                value={limit.toString()}
                onValueChange={(value) => {
                  setLimit(Number(value));
                  setPage(1);
                }}
              >
                <SelectTrigger className="w-20">
                  <SelectValue placeholder={limit.toString()} />
                </SelectTrigger>
                <SelectContent>
                  {[1, 10, 20, 50, 100].map((size) => (
                    <SelectItem key={`limit-${size}`} value={size.toString()}>
                      {size}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            {transactions?.totalPages && transactions?.totalPages > 1 ? (
              <Pagination className="w-auto mx-0">
                <PaginationContent>
                  <PaginationItem>
                    <PaginationPrevious
                      onClick={() =>
                        page === 1
                          ? setPage(Number(transactions?.totalPages))
                          : setPage(page - 1)
                      }
                    />
                  </PaginationItem>
                  <PaginationItem>
                    <PaginationNext
                      onClick={() =>
                        page === Number(transactions?.totalPages)
                          ? setPage(1)
                          : setPage(page + 1)
                      }
                    />
                  </PaginationItem>
                </PaginationContent>
              </Pagination>
            ) : (
              ''
            )}
          </div> */}
      <DeleteUserDialog
        userId={selectedUser?.id ?? ""}
        userFullName={selectedUser?.fullname ?? ""}
        open={openDeleteDialog}
        setOpen={setOpenDeleteDialog}
        mutateDeleteUser={mutateDeleteUser}
        isPending={isPendingDeleteUser}
      />
      <EditUserDialog open={openEditDialog} setOpen={setOpenEditDialog} refetch={refetch} selectedUser={selectedUser}  />
    </Fragment>
  );
}

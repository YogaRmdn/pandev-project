"use client";

import { getTransactions } from "@/app/actions/transaction";
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
import { Fragment } from "react/jsx-runtime";
import TransactionTable from "./transaction-table";
import CreateTransactionDialog from "./create-transaction-dialog";
import { useDisclosure } from "@/hooks/use-disclosure";

export default function TransactionContent() {
  const {
    isPending,
    data: transactions,
    refetch,
  } = useQuery({
    queryFn: getTransactions,
    queryKey: ["transactions"],
  });
  const {open, setOpen} = useDisclosure()
  return (
    <Fragment>
      <Card className="w-full gap-2">
        <CardHeader className="flex items-center justify-between border-b">
          <div>
            <div className="flex gap-2">
              <CardTitle>Data Transaksi</CardTitle>
              {transactions && transactions?.length > 0 ? (
                <Badge className="bg-green-100 text-primary px-1 hover:bg-green-100">
                  Total {transactions?.length}
                </Badge>
              ) : null}
            </div>
            <CardDescription>
              Berikut semua data transaksi yang ada
            </CardDescription>
          </div>
          <div className="space-x-2">
            <Button variant={"outline"}>
              <FilterIcon /> Filter
            </Button>
            <Button className="cursor-pointer" onClick={() => setOpen(true)}>
              <PlusIcon /> Tambah
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <TransactionTable
            isPending={isPending}
            transactions={transactions}
            refetch={refetch}
          />
        </CardContent>
      </Card>
      <CreateTransactionDialog
        open={open}
        setOpen={setOpen}
        refetch={refetch}
      />
    </Fragment>
  );
}

"use client"

import { useState } from "react"
import { getStore, addInvoice } from "@/lib/store"
import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Plus, Eye, Download } from "lucide-react"
import { formatBrDate, formatBrCurrency } from "@/lib/format-utils"

export function InvoicesPage() {
  const store = getStore()
  const [showForm, setShowForm] = useState(false)
  const [selectedInvoice, setSelectedInvoice] = useState<string | null>(null)

  const handleGenerateInvoice = (customerId: string) => {
    const appointmentIds = store.appointments
      .filter((apt) => apt.customerId === customerId && apt.status === "completed")
      .map((apt) => apt.id)

    if (appointmentIds.length === 0) return

    const items = store.appointments
      .filter((apt) => appointmentIds.includes(apt.id))
      .map((apt, idx) => {
        const service = store.services.find((s) => s.id === apt.serviceId)
        return {
          id: `${apt.id}-${idx}`,
          description: service?.name || "Serviço",
          quantity: 1,
          unitPrice: apt.totalCost,
          total: apt.totalCost,
        }
      })

    const subtotal = items.reduce((sum, item) => sum + item.total, 0)
    const tax = subtotal * 0.08
    const total = subtotal + tax

    addInvoice({
      id: Date.now().toString(),
      customerId,
      appointmentIds,
      date: new Date().toISOString().split("T")[0],
      dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split("T")[0],
      items,
      subtotal,
      tax,
      total,
      paid: false,
    })

    const customer = store.customers.find((c) => c.id === customerId)
    if (customer) {
      customer.totalSpent += total
    }
  }

  return (
    <div className="p-8">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-foreground">Faturas</h1>
          <p className="text-muted-foreground">Gerencie faturas e cobranças</p>
        </div>
        <Button onClick={() => setShowForm(!showForm)} className="bg-primary hover:bg-primary/90">
          <Plus className="w-4 h-4 mr-2" />
          Gerar Fatura
        </Button>
      </div>

      {showForm && (
        <Card className="p-6 mb-8 bg-muted/50 border-l-4 border-l-primary">
          <h2 className="text-xl font-bold mb-4">Gerar Fatura</h2>
          <div className="space-y-2 max-h-96 overflow-y-auto">
            {store.customers.map((customer) => (
              <div
                key={customer.id}
                className="flex items-center justify-between p-3 border rounded-lg hover:bg-muted/50 transition"
              >
                <div>
                  <p className="font-medium text-foreground">{customer.name}</p>
                  <p className="text-sm text-muted-foreground">{customer.email}</p>
                </div>
                <Button
                  size="sm"
                  onClick={() => {
                    handleGenerateInvoice(customer.id)
                    setShowForm(false)
                  }}
                  className="bg-primary hover:bg-primary/90"
                >
                  Gerar
                </Button>
              </div>
            ))}
          </div>
        </Card>
      )}

      <div className="space-y-4">
        {store.invoices
          .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
          .map((invoice) => {
            const customer = store.customers.find((c) => c.id === invoice.customerId)

            return (
              <Card key={invoice.id} className="p-6 border-l-4 border-l-accent hover:shadow-md transition">
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <h3 className="text-lg font-bold text-foreground">Fatura #{invoice.id}</h3>
                    <p className="text-sm text-muted-foreground">{customer?.name}</p>
                    <div className="flex gap-6 mt-3 text-sm text-muted-foreground">
                      <span>Emissão: {formatBrDate(invoice.date)}</span>
                      <span>Vencimento: {formatBrDate(invoice.dueDate)}</span>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-2xl font-bold text-primary">{formatBrCurrency(invoice.total)}</p>
                    <span
                      className={`inline-block px-3 py-1 rounded-full text-xs font-medium mt-2 ${
                        invoice.paid ? "bg-chart-3/20 text-chart-3" : "bg-chart-5/20 text-chart-5"
                      }`}
                    >
                      {invoice.paid ? "Paga" : "Pendente"}
                    </span>
                  </div>
                </div>
                <div className="flex gap-2 mt-4">
                  <Button
                    size="sm"
                    variant="outline"
                    onClick={() => setSelectedInvoice(selectedInvoice === invoice.id ? null : invoice.id)}
                  >
                    <Eye className="w-4 h-4 mr-2" />
                    Ver Detalhes
                  </Button>
                  <Button size="sm" variant="outline">
                    <Download className="w-4 h-4 mr-2" />
                    Baixar PDF
                  </Button>
                </div>

                {selectedInvoice === invoice.id && (
                  <div className="mt-4 pt-4 border-t">
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="border-b">
                          <th className="text-left text-foreground font-semibold py-2">Descrição</th>
                          <th className="text-right text-foreground font-semibold py-2">Qtd</th>
                          <th className="text-right text-foreground font-semibold py-2">Preço</th>
                          <th className="text-right text-foreground font-semibold py-2">Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        {invoice.items.map((item) => (
                          <tr key={item.id} className="border-b">
                            <td className="py-2 text-muted-foreground">{item.description}</td>
                            <td className="text-right text-muted-foreground">{item.quantity}</td>
                            <td className="text-right text-muted-foreground">{formatBrCurrency(item.unitPrice)}</td>
                            <td className="text-right font-medium text-foreground">{formatBrCurrency(item.total)}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                    <div className="mt-4 space-y-2 text-sm">
                      <div className="flex justify-end gap-8">
                        <span className="text-muted-foreground">Subtotal:</span>
                        <span className="w-24 text-right font-medium text-foreground">
                          {formatBrCurrency(invoice.subtotal)}
                        </span>
                      </div>
                      <div className="flex justify-end gap-8">
                        <span className="text-muted-foreground">Impostos (8%):</span>
                        <span className="w-24 text-right font-medium text-foreground">
                          {formatBrCurrency(invoice.tax)}
                        </span>
                      </div>
                      <div className="flex justify-end gap-8 border-t pt-2">
                        <span className="text-foreground font-bold">Total:</span>
                        <span className="w-24 text-right font-bold text-primary">
                          {formatBrCurrency(invoice.total)}
                        </span>
                      </div>
                    </div>
                  </div>
                )}
              </Card>
            )
          })}
      </div>
    </div>
  )
}

"use client"

import { getStore } from "@/lib/store"
import { Card } from "@/components/ui/card"
import { BarChart3, PieChart, TrendingUp } from "lucide-react"
import { formatBrCurrency, formatBrDate } from "@/lib/format-utils"

export function ReportsPage() {
  const store = getStore()

  const totalRevenue = store.invoices.reduce((sum, inv) => sum + inv.total, 0)
  const totalAppointments = store.appointments.length
  const completedAppointments = store.appointments.filter((a) => a.status === "completed").length
  const averageInvoiceValue = store.invoices.length > 0 ? totalRevenue / store.invoices.length : 0

  const serviceRevenue = store.services.map((service) => ({
    name: service.name,
    revenue: store.appointments
      .filter((apt) => apt.serviceId === service.id && apt.status === "completed")
      .reduce((sum, apt) => sum + apt.totalCost, 0),
    count: store.appointments.filter((apt) => apt.serviceId === service.id && apt.status === "completed").length,
  }))

  const topCustomers = store.customers.sort((a, b) => b.totalSpent - a.totalSpent).slice(0, 10)

  const statusLabels: Record<string, string> = {
    scheduled: "Agendado",
    completed: "Concluído",
    cancelled: "Cancelado",
    "no-show": "Não Compareceu",
  }

  return (
    <div className="p-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-foreground">Relatórios e Análises</h1>
        <p className="text-muted-foreground">Desempenho e insights do negócio</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card className="p-6 border-t-4 border-t-primary bg-gradient-to-br from-primary/5 to-transparent">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-muted-foreground">Receita Total</p>
              <p className="text-2xl font-bold text-primary mt-2">{formatBrCurrency(totalRevenue)}</p>
            </div>
            <TrendingUp className="w-8 h-8 text-primary" />
          </div>
        </Card>

        <Card className="p-6 border-t-4 border-t-chart-3 bg-gradient-to-br from-chart-3/5 to-transparent">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-muted-foreground">Total de Agendamentos</p>
              <p className="text-2xl font-bold text-chart-3 mt-2">{totalAppointments}</p>
            </div>
            <BarChart3 className="w-8 h-8 text-chart-3" />
          </div>
        </Card>

        <Card className="p-6 border-t-4 border-t-accent bg-gradient-to-br from-accent/5 to-transparent">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-muted-foreground">Agendamentos Concluídos</p>
              <p className="text-2xl font-bold text-accent mt-2">{completedAppointments}</p>
            </div>
            <PieChart className="w-8 h-8 text-accent" />
          </div>
        </Card>

        <Card className="p-6 border-t-4 border-t-chart-5 bg-gradient-to-br from-chart-5/5 to-transparent">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-muted-foreground">Ticket Médio</p>
              <p className="text-2xl font-bold text-chart-5 mt-2">{formatBrCurrency(averageInvoiceValue)}</p>
            </div>
            <TrendingUp className="w-8 h-8 text-chart-5" />
          </div>
        </Card>
      </div>

      {/* Service Revenue & Appointment Status */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <Card className="p-6 border-t-4 border-t-primary">
          <h2 className="text-lg font-bold text-foreground mb-4">Receita por Serviço</h2>
          <div className="space-y-3">
            {serviceRevenue
              .sort((a, b) => b.revenue - a.revenue)
              .map((service) => (
                <div
                  key={service.name}
                  className="flex items-center justify-between p-3 rounded-lg bg-muted/50 hover:bg-muted transition"
                >
                  <div>
                    <p className="font-medium text-foreground">{service.name}</p>
                    <p className="text-sm text-muted-foreground">{service.count} agendamentos</p>
                  </div>
                  <p className="font-semibold text-primary">{formatBrCurrency(service.revenue)}</p>
                </div>
              ))}
          </div>
        </Card>

        {/* Appointment Status Distribution */}
        <Card className="p-6 border-t-4 border-t-accent">
          <h2 className="text-lg font-bold text-foreground mb-4">Status dos Agendamentos</h2>
          <div className="space-y-3">
            {["scheduled", "completed", "cancelled", "no-show"].map((status) => {
              const count = store.appointments.filter((a) => a.status === status).length
              const percentage = totalAppointments > 0 ? ((count / totalAppointments) * 100).toFixed(1) : 0
              const statusColor =
                {
                  completed: "bg-chart-3",
                  scheduled: "bg-primary",
                  cancelled: "bg-destructive",
                  "no-show": "bg-muted",
                }[status] || "bg-muted"
              return (
                <div key={status} className="flex items-center justify-between">
                  <p className="font-medium text-foreground">{statusLabels[status]}</p>
                  <div className="flex items-center gap-3">
                    <div className="w-24 bg-muted rounded-full h-2">
                      <div className={`h-2 rounded-full ${statusColor}`} style={{ width: `${percentage}%` }} />
                    </div>
                    <span className="text-muted-foreground text-sm font-medium">{percentage}%</span>
                  </div>
                </div>
              )
            })}
          </div>
        </Card>
      </div>

      {/* Top Customers */}
      <Card className="p-6 border-t-4 border-t-primary">
        <h2 className="text-lg font-bold text-foreground mb-4">Top 10 Clientes</h2>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b">
                <th className="text-left text-foreground font-semibold py-2">Cliente</th>
                <th className="text-left text-foreground font-semibold py-2">Animais</th>
                <th className="text-right text-foreground font-semibold py-2">Total Gasto</th>
                <th className="text-right text-foreground font-semibold py-2">Data de Cadastro</th>
              </tr>
            </thead>
            <tbody>
              {topCustomers.map((customer) => (
                <tr key={customer.id} className="border-b hover:bg-muted/50 transition">
                  <td className="py-3">
                    <p className="font-medium text-foreground">{customer.name}</p>
                    <p className="text-xs text-muted-foreground">{customer.email}</p>
                  </td>
                  <td className="py-3 text-muted-foreground">{customer.pets.length}</td>
                  <td className="text-right py-3 font-semibold text-primary">
                    {formatBrCurrency(customer.totalSpent)}
                  </td>
                  <td className="text-right py-3 text-muted-foreground">{formatBrDate(customer.joinDate)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  )
}

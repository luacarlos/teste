"use client"

import { getStore } from "@/lib/store"
import { Card } from "@/components/ui/card"
import { Users, Calendar, DollarSign } from "lucide-react"
import { formatBrCurrency } from "@/lib/format-utils"

export function DashboardPage() {
  const store = getStore()

  const totalAppointmentsThisMonth = store.appointments.filter((a) => {
    const date = new Date(a.date)
    const now = new Date()
    return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear()
  }).length

  const totalRevenueThisMonth = store.invoices
    .filter((inv) => {
      const date = new Date(inv.date)
      const now = new Date()
      return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear()
    })
    .reduce((sum, inv) => sum + inv.total, 0)

  const activeCustomers = store.customers.filter((c) => c.status === "active").length

  const stats = [
    { label: "Total de Clientes", value: store.customers.length, icon: Users, color: "bg-primary" },
    { label: "Clientes Ativos", value: activeCustomers, icon: Users, color: "bg-accent" },
    { label: "Agendamentos (Este Mês)", value: totalAppointmentsThisMonth, icon: Calendar, color: "bg-chart-3" },
    {
      label: "Receita (Este Mês)",
      value: formatBrCurrency(totalRevenueThisMonth),
      icon: DollarSign,
      color: "bg-chart-5",
    },
  ]

  return (
    <div className="p-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-foreground">Painel</h1>
        <p className="text-muted-foreground">Bem-vindo! Aqui está o resumo do seu negócio.</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {stats.map((stat, idx) => {
          const Icon = stat.icon
          const colors = [
            { border: "border-primary", bg: "from-primary/10 to-transparent" },
            { border: "border-accent", bg: "from-accent/10 to-transparent" },
            { border: "border-chart-3", bg: "from-chart-3/10 to-transparent" },
            { border: "border-chart-5", bg: "from-chart-5/10 to-transparent" },
          ]
          const colorScheme = colors[idx]

          return (
            <Card key={idx} className={`p-6 border-l-4 ${colorScheme.border} bg-gradient-to-br ${colorScheme.bg}`}>
              <div className="flex items-start justify-between">
                <div>
                  <p className="text-sm text-muted-foreground">{stat.label}</p>
                  <p className="text-2xl font-bold text-foreground mt-2">{stat.value}</p>
                </div>
                <div className={`${stat.color} p-3 rounded-lg`}>
                  <Icon className="w-6 h-6 text-white" />
                </div>
              </div>
            </Card>
          )
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Appointments */}
        <Card className="p-6 border-t-4 border-t-primary">
          <h2 className="text-lg font-bold text-foreground mb-4">Agendamentos Recentes</h2>
          <div className="space-y-3">
            {store.appointments
              .slice(-5)
              .reverse()
              .map((apt) => {
                const customer = store.customers.find((c) => c.id === apt.customerId)
                const service = store.services.find((s) => s.id === apt.serviceId)
                return (
                  <div
                    key={apt.id}
                    className="flex items-center justify-between p-3 bg-muted rounded-lg hover:bg-muted/80 transition"
                  >
                    <div>
                      <p className="font-medium text-foreground">{customer?.name}</p>
                      <p className="text-sm text-muted-foreground">{service?.name}</p>
                    </div>
                    <span
                      className={`px-3 py-1 rounded-full text-xs font-medium ${
                        apt.status === "completed"
                          ? "bg-chart-3/20 text-chart-3"
                          : apt.status === "scheduled"
                            ? "bg-primary/20 text-primary"
                            : "bg-destructive/20 text-destructive"
                      }`}
                    >
                      {apt.status === "completed" ? "Concluído" : apt.status === "scheduled" ? "Agendado" : "Cancelado"}
                    </span>
                  </div>
                )
              })}
            {store.appointments.length === 0 && <p className="text-muted-foreground">Nenhum agendamento ainda</p>}
          </div>
        </Card>

        {/* Top Customers */}
        <Card className="p-6 border-t-4 border-t-accent">
          <h2 className="text-lg font-bold text-foreground mb-4">Principais Clientes</h2>
          <div className="space-y-3">
            {store.customers
              .sort((a, b) => b.totalSpent - a.totalSpent)
              .slice(0, 5)
              .map((customer) => (
                <div
                  key={customer.id}
                  className="flex items-center justify-between p-3 bg-muted rounded-lg hover:bg-muted/80 transition"
                >
                  <div>
                    <p className="font-medium text-foreground">{customer.name}</p>
                    <p className="text-sm text-muted-foreground">{customer.pets.length} animais</p>
                  </div>
                  <p className="font-semibold text-foreground">{formatBrCurrency(customer.totalSpent)}</p>
                </div>
              ))}
            {store.customers.length === 0 && <p className="text-muted-foreground">Nenhum cliente ainda</p>}
          </div>
        </Card>
      </div>
    </div>
  )
}

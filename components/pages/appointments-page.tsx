"use client"

import { useState } from "react"
import { getStore, addAppointment, updateAppointment } from "@/lib/store"
import type { Appointment } from "@/lib/types"
import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Plus, Calendar, Clock } from "lucide-react"
import { formatBrDatetime, formatBrCurrency } from "@/lib/format-utils"

export function AppointmentsPage() {
  const store = getStore()
  const [showForm, setShowForm] = useState(false)
  const [formData, setFormData] = useState<Partial<Appointment>>({
    customerId: "",
    petId: "",
    serviceId: "",
    date: "",
    time: "",
    staffAssigned: "",
    notes: "",
    status: "scheduled",
  })

  const handleSaveAppointment = () => {
    if (formData.customerId && formData.petId && formData.serviceId && formData.date && formData.time) {
      const selectedService = store.services.find((s) => s.id === formData.serviceId)
      addAppointment({
        id: Date.now().toString(),
        duration: selectedService?.duration || 60,
        totalCost: selectedService?.price || 0,
        ...(formData as Appointment),
      })
      setFormData({
        customerId: "",
        petId: "",
        serviceId: "",
        date: "",
        time: "",
        staffAssigned: "",
        notes: "",
        status: "scheduled",
      })
      setShowForm(false)
    }
  }

  const handleStatusChange = (id: string, status: string) => {
    updateAppointment(id, { status: status as any })
  }

  return (
    <div className="p-8">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-foreground">Agendamentos</h1>
          <p className="text-muted-foreground">Agende e gerencie agendamentos</p>
        </div>
        <Button onClick={() => setShowForm(true)} className="bg-primary hover:bg-primary/90">
          <Plus className="w-4 h-4 mr-2" />
          Novo Agendamento
        </Button>
      </div>

      {showForm && (
        <Card className="p-6 mb-8 bg-muted/50 border-l-4 border-l-primary">
          <h2 className="text-xl font-bold mb-4">Agendar Serviço</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select
              className="px-3 py-2 rounded-lg border border-input bg-background"
              value={formData.customerId || ""}
              onChange={(e) => setFormData({ ...formData, customerId: e.target.value })}
            >
              <option value="">Selecione o Cliente</option>
              {store.customers.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.name}
                </option>
              ))}
            </select>

            {formData.customerId && (
              <select
                className="px-3 py-2 rounded-lg border border-input bg-background"
                value={formData.petId || ""}
                onChange={(e) => setFormData({ ...formData, petId: e.target.value })}
              >
                <option value="">Selecione o Animal</option>
                {store.customers
                  .find((c) => c.id === formData.customerId)
                  ?.pets.map((p) => (
                    <option key={p.id} value={p.id}>
                      {p.name}
                    </option>
                  ))}
              </select>
            )}

            <select
              className="px-3 py-2 rounded-lg border border-input bg-background"
              value={formData.serviceId || ""}
              onChange={(e) => setFormData({ ...formData, serviceId: e.target.value })}
            >
              <option value="">Selecione o Serviço</option>
              {store.services.map((s) => (
                <option key={s.id} value={s.id}>
                  {s.name} - {formatBrCurrency(s.price)}
                </option>
              ))}
            </select>

            <select
              className="px-3 py-2 rounded-lg border border-input bg-background"
              value={formData.staffAssigned || ""}
              onChange={(e) => setFormData({ ...formData, staffAssigned: e.target.value })}
            >
              <option value="">Selecione o Profissional</option>
              {store.staff.map((s) => (
                <option key={s.id} value={s.id}>
                  {s.name}
                </option>
              ))}
            </select>

            <Input
              type="date"
              value={formData.date || ""}
              onChange={(e) => setFormData({ ...formData, date: e.target.value })}
            />
            <Input
              type="time"
              value={formData.time || ""}
              onChange={(e) => setFormData({ ...formData, time: e.target.value })}
            />

            <textarea
              className="px-3 py-2 rounded-lg border border-input bg-background col-span-2"
              placeholder="Observações"
              value={formData.notes || ""}
              onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
            />
          </div>
          <div className="flex gap-2 mt-4">
            <Button onClick={handleSaveAppointment} className="bg-primary hover:bg-primary/90">
              Agendar
            </Button>
            <Button variant="outline" onClick={() => setShowForm(false)}>
              Cancelar
            </Button>
          </div>
        </Card>
      )}

      <div className="space-y-4">
        {store.appointments
          .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
          .map((apt) => {
            const customer = store.customers.find((c) => c.id === apt.customerId)
            const pet = customer?.pets.find((p) => p.id === apt.petId)
            const service = store.services.find((s) => s.id === apt.serviceId)
            const staff = store.staff.find((s) => s.id === apt.staffAssigned)

            return (
              <Card key={apt.id} className="p-6 border-l-4 border-l-primary hover:shadow-md transition">
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <h3 className="text-lg font-bold text-foreground">
                      {pet?.name} - {service?.name}
                    </h3>
                    <p className="text-sm text-muted-foreground">{customer?.name}</p>
                    <div className="flex gap-6 mt-3 text-sm text-muted-foreground">
                      <div className="flex items-center gap-1">
                        <Calendar className="w-4 h-4" />
                        {formatBrDatetime(apt.date, apt.time)}
                      </div>
                      <div className="flex items-center gap-1">
                        <Clock className="w-4 h-4" />
                        {apt.duration} min
                      </div>
                      <span>Prof.: {staff?.name}</span>
                    </div>
                  </div>
                  <select
                    value={apt.status}
                    onChange={(e) => handleStatusChange(apt.id, e.target.value)}
                    className={`px-3 py-1 rounded-full text-xs font-medium border-0 ${
                      apt.status === "completed"
                        ? "bg-chart-3/20 text-chart-3"
                        : apt.status === "scheduled"
                          ? "bg-primary/20 text-primary"
                          : apt.status === "cancelled"
                            ? "bg-destructive/20 text-destructive"
                            : "bg-muted text-muted-foreground"
                    }`}
                  >
                    <option value="scheduled">Agendado</option>
                    <option value="completed">Concluído</option>
                    <option value="cancelled">Cancelado</option>
                    <option value="no-show">Não Compareceu</option>
                  </select>
                </div>
              </Card>
            )
          })}
      </div>
    </div>
  )
}

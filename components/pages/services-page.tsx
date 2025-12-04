"use client"

import { useState } from "react"
import { getStore, addService } from "@/lib/store"
import type { Service } from "@/lib/types"
import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Plus, Trash2 } from "lucide-react"
import { formatBrCurrency } from "@/lib/format-utils"

export function ServicesPage() {
  const store = getStore()
  const [showForm, setShowForm] = useState(false)
  const [formData, setFormData] = useState<Partial<Service>>({
    name: "",
    description: "",
    category: "grooming",
    price: 0,
    duration: 60,
    staff: [],
  })

  const handleSaveService = () => {
    if (formData.name && formData.price) {
      addService({
        id: Date.now().toString(),
        ...(formData as Service),
      })
      setFormData({ name: "", description: "", category: "grooming", price: 0, duration: 60, staff: [] })
      setShowForm(false)
    }
  }

  const categoryLabels: Record<string, string> = {
    grooming: "Grooming",
    veterinary: "Veterinário",
    boarding: "Hospedagem",
    training: "Treinamento",
    retail: "Varejo",
  }

  return (
    <div className="p-8">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-foreground">Serviços</h1>
          <p className="text-muted-foreground">Gerencie os serviços oferecidos</p>
        </div>
        <Button onClick={() => setShowForm(true)} className="bg-primary hover:bg-primary/90">
          <Plus className="w-4 h-4 mr-2" />
          Adicionar Serviço
        </Button>
      </div>

      {showForm && (
        <Card className="p-6 mb-8 bg-muted/50 border-l-4 border-l-primary">
          <h2 className="text-xl font-bold mb-4">Novo Serviço</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Input
              placeholder="Nome do Serviço"
              value={formData.name || ""}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
            />
            <select
              className="px-3 py-2 rounded-lg border border-input bg-background"
              value={formData.category || "grooming"}
              onChange={(e) => setFormData({ ...formData, category: e.target.value as any })}
            >
              <option value="grooming">Grooming</option>
              <option value="veterinary">Veterinário</option>
              <option value="boarding">Hospedagem</option>
              <option value="training">Treinamento</option>
              <option value="retail">Varejo</option>
            </select>
            <Input
              type="number"
              placeholder="Preço"
              step="0.01"
              value={formData.price || ""}
              onChange={(e) => setFormData({ ...formData, price: Number.parseFloat(e.target.value) })}
            />
            <Input
              type="number"
              placeholder="Duração (minutos)"
              value={formData.duration || ""}
              onChange={(e) => setFormData({ ...formData, duration: Number.parseInt(e.target.value) })}
            />
            <textarea
              className="px-3 py-2 rounded-lg border border-input bg-background col-span-2"
              placeholder="Descrição"
              value={formData.description || ""}
              onChange={(e) => setFormData({ ...formData, description: e.target.value })}
            />
          </div>
          <div className="flex gap-2 mt-4">
            <Button onClick={handleSaveService} className="bg-primary hover:bg-primary/90">
              Salvar Serviço
            </Button>
            <Button variant="outline" onClick={() => setShowForm(false)}>
              Cancelar
            </Button>
          </div>
        </Card>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {store.services.map((service) => (
          <Card key={service.id} className="p-6 border-t-4 border-t-primary hover:shadow-lg transition">
            <div className="flex items-start justify-between mb-3">
              <div className="flex-1">
                <h3 className="text-lg font-bold text-foreground">{service.name}</h3>
                <p className="text-xs text-muted-foreground uppercase tracking-wider mt-1 font-semibold">
                  {categoryLabels[service.category] || service.category}
                </p>
              </div>
              <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive">
                <Trash2 className="w-4 h-4" />
              </Button>
            </div>
            <p className="text-sm text-muted-foreground mb-4">{service.description}</p>
            <div className="border-t pt-3">
              <div className="flex items-center justify-between">
                <span className="text-2xl font-bold text-primary">{formatBrCurrency(service.price)}</span>
                <span className="text-sm text-muted-foreground font-medium">{service.duration} min</span>
              </div>
            </div>
          </Card>
        ))}
      </div>
    </div>
  )
}

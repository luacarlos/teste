"use client"

import { useState } from "react"
import { getStore, addCustomer, updateCustomer, deleteCustomer, addPet } from "@/lib/store"
import type { Customer, Pet } from "@/lib/types"
import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Trash2, Plus, Edit2 } from "lucide-react"
import { formatBrDate } from "@/lib/format-utils"

export function CustomersPage() {
  const store = getStore()
  const [showForm, setShowForm] = useState(false)
  const [editingId, setEditingId] = useState<string | null>(null)
  const [showPetForm, setShowPetForm] = useState<string | null>(null)

  const [formData, setFormData] = useState<Partial<Customer>>({
    name: "",
    email: "",
    phone: "",
    address: "",
    city: "",
    state: "",
    zipCode: "",
    status: "active",
  })

  const [petFormData, setPetFormData] = useState<Partial<Pet>>({
    name: "",
    species: "dog",
    breed: "",
    dateOfBirth: "",
    weight: 0,
    color: "",
    medicalNotes: "",
  })

  const handleSaveCustomer = () => {
    if (editingId) {
      updateCustomer(editingId, formData)
      setEditingId(null)
    } else {
      addCustomer({
        id: Date.now().toString(),
        pets: [],
        joinDate: new Date().toISOString().split("T")[0],
        totalSpent: 0,
        ...(formData as Customer),
      })
    }
    setFormData({ name: "", email: "", phone: "", address: "", city: "", state: "", zipCode: "", status: "active" })
    setShowForm(false)
  }

  const handleEditCustomer = (customer: Customer) => {
    setFormData(customer)
    setEditingId(customer.id)
    setShowForm(true)
  }

  const handleAddPet = (customerId: string) => {
    if (petFormData.name && petFormData.species) {
      addPet(customerId, {
        id: Date.now().toString(),
        customerId,
        ...(petFormData as Pet),
      })
      setPetFormData({ name: "", species: "dog", breed: "", dateOfBirth: "", weight: 0, color: "", medicalNotes: "" })
      setShowPetForm(null)
    }
  }

  return (
    <div className="p-8">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-3xl font-bold text-foreground">Clientes</h1>
          <p className="text-muted-foreground">Gerencie sua base de clientes</p>
        </div>
        <Button
          onClick={() => {
            setShowForm(true)
            setEditingId(null)
            setFormData({
              name: "",
              email: "",
              phone: "",
              address: "",
              city: "",
              state: "",
              zipCode: "",
              status: "active",
            })
          }}
          className="bg-primary hover:bg-primary/90"
        >
          <Plus className="w-4 h-4 mr-2" />
          Adicionar Cliente
        </Button>
      </div>

      {showForm && (
        <Card className="p-6 mb-8 bg-muted/50 border-l-4 border-l-primary">
          <h2 className="text-xl font-bold mb-4">{editingId ? "Editar" : "Novo"} Cliente</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Input
              placeholder="Nome"
              value={formData.name || ""}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
            />
            <Input
              placeholder="E-mail"
              type="email"
              value={formData.email || ""}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
            />
            <Input
              placeholder="Telefone"
              value={formData.phone || ""}
              onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
            />
            <Input
              placeholder="Endereço"
              value={formData.address || ""}
              onChange={(e) => setFormData({ ...formData, address: e.target.value })}
            />
            <Input
              placeholder="Cidade"
              value={formData.city || ""}
              onChange={(e) => setFormData({ ...formData, city: e.target.value })}
            />
            <Input
              placeholder="Estado"
              value={formData.state || ""}
              onChange={(e) => setFormData({ ...formData, state: e.target.value })}
            />
            <Input
              placeholder="CEP"
              value={formData.zipCode || ""}
              onChange={(e) => setFormData({ ...formData, zipCode: e.target.value })}
            />
          </div>
          <div className="flex gap-2 mt-4">
            <Button onClick={handleSaveCustomer} className="bg-primary hover:bg-primary/90">
              Salvar
            </Button>
            <Button
              variant="outline"
              onClick={() => {
                setShowForm(false)
                setEditingId(null)
              }}
            >
              Cancelar
            </Button>
          </div>
        </Card>
      )}

      <div className="space-y-4">
        {store.customers.map((customer) => (
          <Card key={customer.id} className="p-6 border-l-4 border-l-accent hover:shadow-md transition">
            <div className="flex items-start justify-between mb-4">
              <div className="flex-1">
                <h3 className="text-lg font-bold text-foreground">{customer.name}</h3>
                <p className="text-sm text-muted-foreground">
                  {customer.email} • {customer.phone}
                </p>
                <p className="text-sm text-muted-foreground">
                  {customer.address}, {customer.city}, {customer.state} {customer.zipCode}
                </p>
              </div>
              <div className="flex gap-2">
                <Button variant="outline" size="sm" onClick={() => handleEditCustomer(customer)}>
                  <Edit2 className="w-4 h-4" />
                </Button>
                <Button variant="destructive" size="sm" onClick={() => deleteCustomer(customer.id)}>
                  <Trash2 className="w-4 h-4" />
                </Button>
              </div>
            </div>

            {/* Pets Section */}
            <div className="border-t pt-4">
              <div className="flex items-center justify-between mb-3">
                <h4 className="font-semibold text-foreground">Animais ({customer.pets.length})</h4>
                <Button size="sm" variant="outline" onClick={() => setShowPetForm(customer.id)}>
                  <Plus className="w-3 h-3 mr-1" />
                  Adicionar
                </Button>
              </div>

              {showPetForm === customer.id && (
                <div className="bg-muted rounded-lg p-4 mb-3">
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <Input
                      placeholder="Nome do Animal"
                      value={petFormData.name || ""}
                      onChange={(e) => setPetFormData({ ...petFormData, name: e.target.value })}
                    />
                    <select
                      className="px-3 py-2 rounded-lg border border-input bg-background"
                      value={petFormData.species || "dog"}
                      onChange={(e) => setPetFormData({ ...petFormData, species: e.target.value as any })}
                    >
                      <option value="dog">Cachorro</option>
                      <option value="cat">Gato</option>
                      <option value="rabbit">Coelho</option>
                      <option value="bird">Pássaro</option>
                      <option value="hamster">Hamster</option>
                      <option value="other">Outro</option>
                    </select>
                    <Input
                      placeholder="Raça"
                      value={petFormData.breed || ""}
                      onChange={(e) => setPetFormData({ ...petFormData, breed: e.target.value })}
                    />
                    <Input
                      placeholder="Cor"
                      value={petFormData.color || ""}
                      onChange={(e) => setPetFormData({ ...petFormData, color: e.target.value })}
                    />
                    <Input
                      type="number"
                      placeholder="Peso (kg)"
                      value={petFormData.weight || ""}
                      onChange={(e) => setPetFormData({ ...petFormData, weight: Number.parseFloat(e.target.value) })}
                    />
                    <Input
                      type="date"
                      value={petFormData.dateOfBirth || ""}
                      onChange={(e) => setPetFormData({ ...petFormData, dateOfBirth: e.target.value })}
                    />
                  </div>
                  <div className="flex gap-2 mt-3">
                    <Button
                      size="sm"
                      onClick={() => handleAddPet(customer.id)}
                      className="bg-primary hover:bg-primary/90"
                    >
                      Salvar Animal
                    </Button>
                    <Button size="sm" variant="outline" onClick={() => setShowPetForm(null)}>
                      Cancelar
                    </Button>
                  </div>
                </div>
              )}

              <div className="space-y-2">
                {customer.pets.map((pet) => (
                  <div key={pet.id} className="p-3 bg-muted rounded-lg border border-primary/20">
                    <p className="font-medium text-foreground">
                      {pet.name} -{" "}
                      {pet.species === "dog"
                        ? "Cachorro"
                        : pet.species === "cat"
                          ? "Gato"
                          : pet.species.charAt(0).toUpperCase() + pet.species.slice(1)}{" "}
                      ({pet.breed})
                    </p>
                    <p className="text-sm text-muted-foreground">
                      Peso: {pet.weight} kg • Nasc.: {formatBrDate(pet.dateOfBirth)}
                    </p>
                  </div>
                ))}
              </div>
            </div>
          </Card>
        ))}
      </div>
    </div>
  )
}

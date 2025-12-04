export interface Pet {
  id: string
  name: string
  species: "dog" | "cat" | "rabbit" | "bird" | "hamster" | "other"
  breed: string
  dateOfBirth: string
  weight: number
  color: string
  customerId: string
  medicalNotes: string
  lastVaccine?: string
  lastCheckup?: string
}

export interface Customer {
  id: string
  name: string
  email: string
  phone: string
  address: string
  city: string
  state: string
  zipCode: string
  pets: Pet[]
  joinDate: string
  totalSpent: number
  status: "active" | "inactive"
}

export interface Service {
  id: string
  name: string
  description: string
  category: "grooming" | "veterinary" | "boarding" | "training" | "retail"
  price: number
  duration: number // in minutes
  staff: string[]
}

export interface Appointment {
  id: string
  customerId: string
  petId: string
  serviceId: string
  date: string
  time: string
  duration: number
  status: "scheduled" | "completed" | "cancelled" | "no-show"
  staffAssigned: string
  notes: string
  totalCost: number
}

export interface Invoice {
  id: string
  customerId: string
  appointmentIds: string[]
  date: string
  dueDate: string
  items: InvoiceItem[]
  subtotal: number
  tax: number
  total: number
  paid: boolean
  paidDate?: string
  paymentMethod?: string
}

export interface InvoiceItem {
  id: string
  description: string
  quantity: number
  unitPrice: number
  total: number
}

export interface Staff {
  id: string
  name: string
  email: string
  phone: string
  role: "admin" | "groomer" | "veterinarian" | "attendant" | "finance"
  specialties: string[]
  joinDate: string
}

export interface InventoryItem {
  id: string
  name: string
  category: string
  quantity: number
  reorderLevel: number
  cost: number
  retailPrice: number
  supplier: string
}

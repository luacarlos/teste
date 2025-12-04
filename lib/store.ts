import type { Customer, Pet, Service, Appointment, Invoice, Staff, InventoryItem } from "./types"

// Initialize with some sample data
const initialCustomers: Customer[] = [
  {
    id: "1",
    name: "John Smith",
    email: "john@example.com",
    phone: "555-0001",
    address: "123 Main St",
    city: "Springfield",
    state: "IL",
    zipCode: "62701",
    pets: [],
    joinDate: "2024-01-15",
    totalSpent: 450,
    status: "active",
  },
]

const initialServices: Service[] = [
  {
    id: "1",
    name: "Basic Grooming",
    description: "Bath and trim",
    category: "grooming",
    price: 50,
    duration: 60,
    staff: [],
  },
  {
    id: "2",
    name: "Full Grooming",
    description: "Complete grooming service",
    category: "grooming",
    price: 80,
    duration: 120,
    staff: [],
  },
  {
    id: "3",
    name: "Veterinary Checkup",
    description: "General health checkup",
    category: "veterinary",
    price: 75,
    duration: 30,
    staff: [],
  },
  {
    id: "4",
    name: "Vaccination",
    description: "Pet vaccination service",
    category: "veterinary",
    price: 40,
    duration: 15,
    staff: [],
  },
  {
    id: "5",
    name: "Boarding (Per Day)",
    description: "Daily boarding service",
    category: "boarding",
    price: 35,
    duration: 1440,
    staff: [],
  },
]

const initialStaff: Staff[] = [
  {
    id: "1",
    name: "Sarah Johnson",
    email: "sarah@petshop.com",
    phone: "555-1001",
    role: "admin",
    specialties: ["management"],
    joinDate: "2023-01-01",
  },
  {
    id: "2",
    name: "Mike Chen",
    email: "mike@petshop.com",
    phone: "555-1002",
    role: "groomer",
    specialties: ["dog grooming", "cat grooming"],
    joinDate: "2023-06-01",
  },
  {
    id: "3",
    name: "Dr. Emily Davis",
    email: "emily@petshop.com",
    phone: "555-1003",
    role: "veterinarian",
    specialties: ["general practice", "surgery"],
    joinDate: "2023-03-01",
  },
]

let store = {
  customers: initialCustomers,
  services: initialServices,
  appointments: [] as Appointment[],
  invoices: [] as Invoice[],
  staff: initialStaff,
  inventory: [] as InventoryItem[],
}

export const getStore = () => store

export const updateStore = (newStore: typeof store) => {
  store = newStore
}

export const addCustomer = (customer: Customer) => {
  store.customers.push(customer)
}

export const updateCustomer = (id: string, updates: Partial<Customer>) => {
  const customer = store.customers.find((c) => c.id === id)
  if (customer) {
    Object.assign(customer, updates)
  }
}

export const deleteCustomer = (id: string) => {
  store.customers = store.customers.filter((c) => c.id !== id)
}

export const addPet = (customerId: string, pet: Pet) => {
  const customer = store.customers.find((c) => c.id === customerId)
  if (customer) {
    customer.pets.push(pet)
  }
}

export const addService = (service: Service) => {
  store.services.push(service)
}

export const addAppointment = (appointment: Appointment) => {
  store.appointments.push(appointment)
}

export const updateAppointment = (id: string, updates: Partial<Appointment>) => {
  const appointment = store.appointments.find((a) => a.id === id)
  if (appointment) {
    Object.assign(appointment, updates)
  }
}

export const addInvoice = (invoice: Invoice) => {
  store.invoices.push(invoice)
}

export const addInventoryItem = (item: InventoryItem) => {
  store.inventory.push(item)
}

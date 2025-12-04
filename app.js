// Local Storage Management
const API_BASE_URL = window.location.origin

// Initialize local data
let appData = {
  customers: [],
  pets: [],
  appointments: [],
  services: [],
  invoices: [],
}

// Load data from localStorage or initialize
function loadData() {
  const saved = localStorage.getItem("petshop_data")
  if (saved) {
    appData = JSON.parse(saved)
  }
}

// Save data to localStorage
function saveData() {
  localStorage.setItem("petshop_data", JSON.stringify(appData))
}

// Carregar dados do servidor PHP
async function carregarDados() {
  try {
    const [clientes, animais, servicos, agendamentos, faturas] = await Promise.all([
      fetch(`${API_BASE_URL}/api.php?acao=obter_clientes`).then((r) => r.json()),
      fetch(`${API_BASE_URL}/api.php?acao=obter_animais`).then((r) => r.json()),
      fetch(`${API_BASE_URL}/api.php?acao=obter_servicos`).then((r) => r.json()),
      fetch(`${API_BASE_URL}/api.php?acao=obter_agendamentos`).then((r) => r.json()),
      fetch(`${API_BASE_URL}/api.php?acao=obter_faturas`).then((r) => r.json()),
    ])

    appData.customers = clientes.map((c) => ({
      id: c.id,
      name: c.nome,
      phone: c.telefone,
      email: c.email,
      address: c.endereco,
      city: c.cidade,
    }))

    appData.pets = animais.map((p) => ({
      id: p.id,
      customer_id: p.cliente_id,
      name: p.nome,
      breed: p.raca,
      type: p.tipo,
      birthdate: p.data_nascimento,
      weight: p.peso,
    }))

    appData.services = servicos.map((s) => ({
      id: s.id,
      name: s.nome,
      description: s.descricao,
      price: Number.parseFloat(s.preco),
      duration: s.duracao_minutos,
    }))

    appData.appointments = agendamentos.map((a) => ({
      id: a.id,
      customer_id: a.cliente_id,
      pet_id: a.animal_id,
      service_id: a.servico_id,
      date: a.data_agendamento.split(" ")[0],
      time: a.data_agendamento.split(" ")[1] || "00:00",
      notes: a.observacoes,
      status: a.status,
    }))

    appData.invoices = faturas.map((f) => ({
      id: f.id,
      customer_id: f.cliente_id,
      date: f.created_at.split(" ")[0],
      total: Number.parseFloat(f.valor_total),
      status: f.status,
    }))
  } catch (erro) {
    console.error("Erro ao carregar dados:", erro)
    alert("Erro ao carregar dados do servidor")
  }
}

// Navigation
document.querySelectorAll(".nav-item").forEach((item) => {
  item.addEventListener("click", (e) => {
    if (item.href.includes("logout")) return
    e.preventDefault()
    const pageName = item.dataset.page
    navigateToPage(pageName)
  })
})

function navigateToPage(pageName) {
  document.querySelectorAll(".nav-item").forEach((item) => {
    item.classList.remove("active")
  })
  document.querySelector(`[data-page="${pageName}"]`).classList.add("active")

  document.querySelectorAll(".page").forEach((page) => {
    page.classList.remove("active")
  })
  document.getElementById(`page-${pageName}`).classList.add("active")

  const titles = {
    dashboard: "Painel de Controle",
    customers: "Gerenciar Clientes",
    pets: "Gerenciar Pets",
    appointments: "Agendamentos",
    services: "Serviços Oferecidos",
    invoices: "Faturas",
    reports: "Relatórios e Análises",
  }
  document.querySelector(".page-title").textContent = titles[pageName] || "Painel de Controle"

  if (pageName === "dashboard") loadDashboard()
  if (pageName === "customers") loadCustomers()
  if (pageName === "pets") loadPets()
  if (pageName === "appointments") loadAppointments()
  if (pageName === "services") loadServices()
  if (pageName === "invoices") loadInvoices()
}

// Dashboard
function loadDashboard() {
  document.getElementById("total-customers").textContent = appData.customers.length
  document.getElementById("total-pets").textContent = appData.pets.length

  const today = new Date().toISOString().split("T")[0]
  const todayAppointments = appData.appointments.filter((a) => a.date === today).length
  document.getElementById("today-appointments").textContent = todayAppointments

  const monthRevenue = appData.invoices
    .filter((inv) => inv.date.substring(0, 7) === today.substring(0, 7) && inv.status === "pago")
    .reduce((sum, inv) => sum + (inv.total || 0), 0)
  document.getElementById("month-revenue").textContent = formatarMoeda(monthRevenue)

  loadUpcomingAppointments()
}

function loadUpcomingAppointments() {
  const tbody = document.getElementById("upcoming-appointments")
  const upcoming = appData.appointments
    .sort((a, b) => new Date(`${a.date}T${a.time}`) - new Date(`${b.date}T${b.time}`))
    .slice(0, 5)

  if (upcoming.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhum agendamento próximo</td></tr>'
    return
  }

  tbody.innerHTML = upcoming
    .map((apt) => {
      const customer = appData.customers.find((c) => c.id == apt.customer_id)
      const pet = appData.pets.find((p) => p.id == apt.pet_id)
      const service = appData.services.find((s) => s.id == apt.service_id)

      return `
        <tr>
          <td>${customer?.name || "N/A"}</td>
          <td>${pet?.name || "N/A"}</td>
          <td>${service?.name || "N/A"}</td>
          <td>${formatarData(apt.date)} às ${apt.time}</td>
          <td>
            <button class="btn btn-primary btn-small" onclick="editarAgendamento(${apt.id})">Editar</button>
          </td>
        </tr>
      `
    })
    .join("")
}

// Customers
function loadCustomers() {
  const tbody = document.getElementById("customers-table")

  if (appData.customers.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhum cliente cadastrado</td></tr>'
    return
  }

  tbody.innerHTML = appData.customers
    .map((customer) => {
      const customerPets = appData.pets.filter((p) => p.customer_id === customer.id)
      return `
        <tr>
          <td><strong>${customer.name}</strong></td>
          <td>${customer.phone}</td>
          <td>${customer.email || "-"}</td>
          <td>${customerPets.length}</td>
          <td>
            <button class="btn btn-primary btn-small" onclick="editarCliente('${customer.id}')">Editar</button>
            <button class="btn btn-secondary btn-small" onclick="deletarCliente('${customer.id}')">Deletar</button>
          </td>
        </tr>
      `
    })
    .join("")

  popularSelectsDeCliente()
}

function popularSelectsDeCliente() {
  document.querySelectorAll('select[name="customer_id"]').forEach((select) => {
    select.innerHTML =
      '<option value="">Selecione um cliente</option>' +
      appData.customers.map((c) => `<option value="${c.id}">${c.name}</option>`).join("")
  })
}

function mostrarModalDeCliente() {
  document.getElementById("customer-form").reset()
  document.getElementById("customer-modal").classList.add("show")
}

function handleCustomerSubmit(e) {
  e.preventDefault()
  const form = document.getElementById("customer-form")
  const formData = new FormData(form)

  const customer = {
    id: Date.now().toString(),
    name: formData.get("name"),
    phone: formData.get("phone"),
    email: formData.get("email"),
    address: formData.get("address"),
    city: formData.get("city"),
  }

  appData.customers.push(customer)
  saveData()
  fecharModal("customer-modal")
  loadCustomers()
  loadDashboard()
  alert("Cliente adicionado com sucesso!")
}

function editarCliente(id) {
  alert("Função de edição será implementada em breve!")
}

function deletarCliente(id) {
  if (
    confirm(
      "Tem certeza que deseja deletar este cliente? Todos os pets e agendamentos associados também serão removidos.",
    )
  ) {
    appData.customers = appData.customers.filter((c) => c.id !== id)
    appData.pets = appData.pets.filter((p) => p.customer_id !== id)
    appData.appointments = appData.appointments.filter((a) => a.customer_id !== id)
    saveData()
    loadCustomers()
    loadDashboard()
  }
}

// Pets
function loadPets() {
  const grid = document.getElementById("pets-grid")

  if (appData.pets.length === 0) {
    grid.innerHTML = '<p class="text-center">Nenhum pet cadastrado</p>'
    return
  }

  grid.innerHTML = appData.pets
    .map((pet) => {
      const customer = appData.customers.find((c) => c.id === pet.customer_id)
      const petTypes = {
        dog: "🐕 Cachorro",
        cat: "🐱 Gato",
        bird: "🦜 Pássaro",
        other: "🐾 Outro",
      }

      return `
        <div class="pet-card">
          <h4>${pet.name}</h4>
          <p><strong>Tipo:</strong> ${petTypes[pet.type] || pet.type}</p>
          <p><strong>Raça:</strong> ${pet.breed || "-"}</p>
          <p><strong>Proprietário:</strong> ${customer?.name || "N/A"}</p>
          ${pet.birthdate ? `<p><strong>Nascimento:</strong> ${formatarData(pet.birthdate)}</p>` : ""}
          <p><strong>Peso:</strong> ${pet.weight || "-"}</p>
          <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: center;">
            <button class="btn btn-primary btn-small" onclick="editarPet('${pet.id}')">Editar</button>
            <button class="btn btn-secondary btn-small" onclick="deletarPet('${pet.id}')">Deletar</button>
          </div>
        </div>
      `
    })
    .join("")

  popularSelectsDePet()
}

function popularSelectsDePet() {
  document.querySelectorAll('select[name="pet_id"]').forEach((select) => {
    select.innerHTML =
      '<option value="">Selecione um pet</option>' +
      appData.pets.map((p) => `<option value="${p.id}">${p.name}</option>`).join("")
  })
}

function mostrarModalDePet() {
  document.getElementById("pet-form").reset()
  popularSelectsDeCliente()
  document.getElementById("pet-modal").classList.add("show")
}

function handlePetSubmit(e) {
  e.preventDefault()
  const form = document.getElementById("pet-form")
  const formData = new FormData(form)

  const pet = {
    id: Date.now().toString(),
    name: formData.get("name"),
    type: formData.get("type"),
    breed: formData.get("breed"),
    customer_id: formData.get("customer_id"),
    birthdate: formData.get("birthdate"),
    weight: Number.parseFloat(formData.get("weight")),
  }

  appData.pets.push(pet)
  saveData()
  fecharModal("pet-modal")
  loadPets()
  loadDashboard()
  alert("Pet adicionado com sucesso!")
}

function editarPet(id) {
  alert("Função de edição será implementada em breve!")
}

function deletarPet(id) {
  if (confirm("Tem certeza que deseja deletar este pet?")) {
    appData.pets = appData.pets.filter((p) => p.id !== id)
    appData.appointments = appData.appointments.filter((a) => a.pet_id !== id)
    saveData()
    loadPets()
    loadDashboard()
  }
}

// Appointments
function loadAppointments() {
  const tbody = document.getElementById("appointments-table")
  const dateInput = document.getElementById("appointment-date")

  const selectedDate = dateInput.value || new Date().toISOString().split("T")[0]
  const filtered = appData.appointments
    .filter((a) => a.date === selectedDate)
    .sort((a, b) => a.time.localeCompare(b.time))

  if (filtered.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center">Nenhum agendamento para ${formatarData(selectedDate)}</td></tr>`
    return
  }

  tbody.innerHTML = filtered
    .map((apt) => {
      const customer = appData.customers.find((c) => c.id == apt.customer_id)
      const pet = appData.pets.find((p) => p.id == apt.pet_id)
      const service = appData.services.find((s) => s.id == apt.service_id)
      const statusText = apt.status === "concluído" ? "Concluído" : "Agendado"
      const statusColor = apt.status === "concluído" ? "#d1fae5" : "#fef3c7"

      return `
        <tr>
          <td><strong>${apt.time}</strong></td>
          <td>${customer?.name || "N/A"}</td>
          <td>${pet?.name || "N/A"}</td>
          <td>${service?.name || "N/A"}</td>
          <td><span style="background: ${statusColor}; padding: 4px 8px; border-radius: 4px; color: #333; font-weight: 500; font-size: 12px; text-transform: uppercase;">${statusText}</span></td>
          <td>
            <button class="btn btn-primary btn-small" onclick="editarAgendamento(${apt.id})">Editar</button>
            <button class="btn btn-secondary btn-small" onclick="deletarAgendamento(${apt.id})">Deletar</button>
          </td>
        </tr>
      `
    })
    .join("")
}

document.addEventListener("DOMContentLoaded", () => {
  const dateInput = document.getElementById("appointment-date")
  if (dateInput) {
    dateInput.value = new Date().toISOString().split("T")[0]
    dateInput.addEventListener("change", loadAppointments)
  }
})

function mostrarModalDeAgendamento() {
  document.getElementById("appointment-form").reset()
  popularSelectsDeCliente()
  popularSelectsDePet()
  popularSelectsDeServico()
  document.getElementById("appointment-modal").classList.add("show")
}

function handleAppointmentSubmit(e) {
  e.preventDefault()
  const form = document.getElementById("appointment-form")
  const formData = new FormData(form)

  const appointment = {
    id: Date.now().toString(),
    customer_id: formData.get("customer_id"),
    pet_id: formData.get("pet_id"),
    service_id: formData.get("service_id"),
    date: formData.get("date"),
    time: formData.get("time"),
    notes: formData.get("notes"),
    status: "agendado",
  }

  appData.appointments.push(appointment)
  saveData()
  fecharModal("appointment-modal")
  loadAppointments()
  loadDashboard()
  alert("Agendamento criado com sucesso!")
}

function editarAgendamento(id) {
  alert("Função de edição será implementada em breve!")
}

function deletarAgendamento(id) {
  if (confirm("Tem certeza que deseja cancelar este agendamento?")) {
    appData.appointments = appData.appointments.filter((a) => a.id !== id)
    saveData()
    loadAppointments()
    loadDashboard()
  }
}

// Services
function loadServices() {
  const tbody = document.getElementById("services-table")

  if (appData.services.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhum serviço cadastrado</td></tr>'
    return
  }

  tbody.innerHTML = appData.services
    .map(
      (service) => `
        <tr>
          <td><strong>${service.name}</strong></td>
          <td>${service.description || "-"}</td>
          <td>R$ ${formatarMoeda(service.price || 0)}</td>
          <td>${service.duration} min</td>
          <td>
            <button class="btn btn-primary btn-small" onclick="editarServico('${service.id}')">Editar</button>
            <button class="btn btn-secondary btn-small" onclick="deletarServico('${service.id}')">Deletar</button>
          </td>
        </tr>
      `,
    )
    .join("")

  popularSelectsDeServico()
}

function popularSelectsDeServico() {
  document.querySelectorAll('select[name="service_id"]').forEach((select) => {
    select.innerHTML =
      '<option value="">Selecione um serviço</option>' +
      appData.services.map((s) => `<option value="${s.id}">${s.name}</option>`).join("")
  })
}

function mostrarModalDeServico() {
  document.getElementById("service-form").reset()
  document.getElementById("service-modal").classList.add("show")
}

function handleServiceSubmit(e) {
  e.preventDefault()
  const form = document.getElementById("service-form")
  const formData = new FormData(form)

  const service = {
    id: Date.now().toString(),
    name: formData.get("name"),
    description: formData.get("description"),
    price: Number.parseFloat(formData.get("price")),
    duration: Number.parseInt(formData.get("duration")),
  }

  appData.services.push(service)
  saveData()
  fecharModal("service-modal")
  loadServices()
  alert("Serviço adicionado com sucesso!")
}

function editarServico(id) {
  alert("Função de edição será implementada em breve!")
}

function deletarServico(id) {
  if (confirm("Tem certeza que deseja deletar este serviço?")) {
    appData.services = appData.services.filter((s) => s.id !== id)
    saveData()
    loadServices()
  }
}

// Invoices
function loadInvoices() {
  const tbody = document.getElementById("invoices-table")

  if (appData.invoices.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhuma fatura registrada</td></tr>'
    return
  }

  tbody.innerHTML = appData.invoices
    .sort((a, b) => new Date(b.date) - new Date(a.date))
    .map((invoice) => {
      const customer = appData.customers.find((c) => c.id === invoice.customer_id)
      const statusColor = invoice.status === "paid" ? "#d1fae5" : "#fef3c7"
      const statusText = invoice.status === "paid" ? "Pago" : "Pendente"

      return `
        <tr>
          <td><strong>${invoice.id.substring(0, 8)}</strong></td>
          <td>${customer?.name || "N/A"}</td>
          <td>${formatarData(invoice.date)}</td>
          <td>R$ ${formatarMoeda(invoice.total || 0)}</td>
          <td><span style="background: ${statusColor}; padding: 4px 8px; border-radius: 4px; color: #333; font-weight: 500; font-size: 12px; text-transform: uppercase;">${statusText}</span></td>
          <td>
            <button class="btn btn-primary btn-small" onclick="editarFatura('${invoice.id}')">Editar</button>
            <button class="btn btn-secondary btn-small" onclick="deletarFatura('${invoice.id}')">Deletar</button>
          </td>
        </tr>
      `
    })
    .join("")
}

function mostrarModalDeFatura() {
  document.getElementById("invoice-form").reset()
  popularSelectsDeCliente()
  document.getElementById("invoice-modal").classList.add("show")
}

function handleInvoiceSubmit(e) {
  e.preventDefault()
  const form = document.getElementById("invoice-form")
  const formData = new FormData(form)

  const invoice = {
    id: Date.now().toString(),
    customer_id: formData.get("customer_id"),
    date: formData.get("date"),
    description: formData.get("description"),
    total: Number.parseFloat(formData.get("total")),
    status: formData.get("status"),
  }

  appData.invoices.push(invoice)
  saveData()
  fecharModal("invoice-modal")
  loadInvoices()
  loadDashboard()
  alert("Fatura registrada com sucesso!")
}

function editarFatura(id) {
  alert("Função de edição será implementada em breve!")
}

function deletarFatura(id) {
  if (confirm("Tem certeza que deseja deletar esta fatura?")) {
    appData.invoices = appData.invoices.filter((i) => i.id !== id)
    saveData()
    loadInvoices()
    loadDashboard()
  }
}

// Utility Functions
function formatarData(dateString) {
  if (!dateString) return ""
  const [year, month, day] = dateString.split("-")
  return `${day}/${month}/${year}`
}

function formatarMoeda(value) {
  return "R$ " + Number.parseFloat(value).toFixed(2).replace(".", ",")
}

// Modal Functions
function fecharModal(modalId) {
  document.getElementById(modalId).classList.remove("show")
}

// Click outside modal to close
window.addEventListener("click", (e) => {
  if (e.target.classList.contains("modal")) {
    e.target.classList.remove("show")
  }
})

// Initialize app
document.addEventListener("DOMContentLoaded", () => {
  carregarDados().then(() => {
    navigateToPage("dashboard")
  })
})

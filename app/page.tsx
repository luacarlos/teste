"use client"

import { useState } from "react"
import { DashboardLayout } from "@/components/dashboard-layout"
import { CustomersPage } from "@/components/pages/customers-page"
import { AppointmentsPage } from "@/components/pages/appointments-page"
import { ServicesPage } from "@/components/pages/services-page"
import { InvoicesPage } from "@/components/pages/invoices-page"
import { ReportsPage } from "@/components/pages/reports-page"
import { DashboardPage } from "@/components/pages/dashboard-page"

export default function Home() {
  const [currentPage, setCurrentPage] = useState<
    "dashboard" | "customers" | "appointments" | "services" | "invoices" | "reports"
  >("dashboard")

  const renderPage = () => {
    switch (currentPage) {
      case "customers":
        return <CustomersPage />
      case "appointments":
        return <AppointmentsPage />
      case "services":
        return <ServicesPage />
      case "invoices":
        return <InvoicesPage />
      case "reports":
        return <ReportsPage />
      default:
        return <DashboardPage />
    }
  }

  return (
    <DashboardLayout currentPage={currentPage} onPageChange={setCurrentPage}>
      {renderPage()}
    </DashboardLayout>
  )
}

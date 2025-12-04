"use client"

import type React from "react"

import { Button } from "@/components/ui/button"
import { LayoutDashboard, Users, Calendar, Wrench, FileText, BarChart3, LogOut } from "lucide-react"

interface DashboardLayoutProps {
  currentPage: string
  onPageChange: (page: any) => void
  children: React.ReactNode
}

export function DashboardLayout({ currentPage, onPageChange, children }: DashboardLayoutProps) {
  return (
    <div className="flex h-screen bg-background">
      {/* Sidebar */}
      <aside className="w-64 bg-sidebar border-r border-sidebar-border flex flex-col">
        <div className="p-6 border-b border-sidebar-border">
          <h1 className="text-2xl font-bold text-sidebar-foreground">PetShop CRM</h1>
          <p className="text-sm text-sidebar-foreground/60">Gestão de Negócios</p>
        </div>

        <nav className="flex-1 p-4 space-y-2">
          {[
            { id: "dashboard", label: "Painel", icon: LayoutDashboard },
            { id: "customers", label: "Clientes", icon: Users },
            { id: "appointments", label: "Agendamentos", icon: Calendar },
            { id: "services", label: "Serviços", icon: Wrench },
            { id: "invoices", label: "Faturas", icon: FileText },
            { id: "reports", label: "Relatórios", icon: BarChart3 },
          ].map((item) => {
            const Icon = item.icon
            const isActive = currentPage === item.id
            return (
              <button
                key={item.id}
                onClick={() => onPageChange(item.id)}
                className={`w-full flex items-center gap-3 px-4 py-2 rounded-lg transition ${
                  isActive
                    ? "bg-sidebar-primary text-sidebar-primary-foreground font-semibold"
                    : "text-sidebar-foreground hover:bg-sidebar-accent/30"
                }`}
              >
                <Icon className="w-5 h-5" />
                <span className="font-medium">{item.label}</span>
              </button>
            )
          })}
        </nav>

        <div className="p-4 border-t border-sidebar-border">
          <Button variant="outline" className="w-full bg-transparent hover:bg-sidebar-accent/20" size="sm">
            <LogOut className="w-4 h-4 mr-2" />
            Sair
          </Button>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 overflow-auto">{children}</main>
    </div>
  )
}

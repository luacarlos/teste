export function formatBrDate(dateString: string): string {
  try {
    const date = new Date(dateString)
    const day = String(date.getDate()).padStart(2, "0")
    const month = String(date.getMonth() + 1).padStart(2, "0")
    const year = date.getFullYear()
    return `${day}/${month}/${year}`
  } catch {
    return dateString
  }
}

export function formatBrCurrency(value: number): string {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value)
}

export function formatBrDatetime(dateString: string, timeString: string): string {
  return `${formatBrDate(dateString)} às ${timeString}`
}

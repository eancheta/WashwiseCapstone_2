import type { LucideIcon } from 'lucide-vue-next'
import type { Config } from 'ziggy-js'

/**
 * User model coming from backend
 */
export interface User {
  id: number
  name: string
  email: string
  avatar?: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
}

/**
 * Shop model for car wash
 */
export interface Shop {
  id: number
  owner_id: number
  name: string
  address: string
  district?: string | null
  description?: string | null
  services_offered?: string | null
  logo?: string | null
  qr_code?: string | null
  status: 'open' | 'closed' | 'pending'
  created_at: string
  updated_at: string
}

/**
 * Auth wrapper
 */
export interface Auth {
  user: User | null
}

/**
 * Breadcrumb type for navigation
 */
export interface BreadcrumbItem {
  title: string
  href: string
}

/**
 * Sidebar/Navigation item
 */
export interface NavItem {
  title: string
  href: string
  icon?: LucideIcon
  isActive?: boolean
}

/**
 * Generic PageProps type used with Inertia
 */
export type AppPageProps<
  T extends Record<string, unknown> = Record<string, unknown>
> = T & {
  name: string
  quote: { message: string; author: string }
  auth: Auth
  ziggy: Config & { location: string }
  sidebarOpen: boolean
  shop?: Shop | null
}

/**
 * Breadcrumb alias
 */
export type BreadcrumbItemType = BreadcrumbItem

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const sidebarOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: '📊' },
    { name: 'Productos', href: '/admin/productos', icon: '🧊' },
    { name: 'Categorías', href: '/admin/categorias', icon: '🏷️' },
    { name: 'Insumos', href: '/admin/insumos', icon: '📦' },
    { name: 'Recetas', href: '/admin/recetas', icon: '📋' },
    { name: 'Ventas', href: '/admin/ventas', icon: '💰' },
    { name: 'Gastos', href: '/admin/gastos', icon: '🛒' },
    { name: 'Reportes', href: '/admin/reportes', icon: '📈' },
    { name: 'Leads', href: '/admin/leads', icon: '📬' },
];
</script>

<template>
    <div class="min-h-screen bg-sky-50 flex">

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-sky-900 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto"
        >
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 bg-sky-950">
                <span class="text-xl font-bold tracking-tight">🧊 Hielitos 3C</span>
                <button @click="sidebarOpen = false" class="lg:hidden text-sky-300 hover:text-white">✕</button>
            </div>

            <!-- Nav -->
            <nav class="mt-6 px-3 space-y-1">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sky-100 hover:bg-sky-700 hover:text-white transition-colors text-sm font-medium"
                    :class="{ 'bg-sky-700 text-white': $page.url.startsWith(item.href) }"
                >
                    <span>{{ item.icon }}</span>
                    <span>{{ item.name }}</span>
                </Link>
            </nav>

            <!-- User info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-sky-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-sky-500 flex items-center justify-center text-sm font-bold">
                        {{ user?.name?.charAt(0) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                        <p class="text-xs text-sky-300 capitalize">{{ user?.role }}</p>
                    </div>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="text-sky-300 hover:text-white text-xs"
                    >
                        Salir
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
        />

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top bar -->
            <header class="h-16 bg-white border-b border-sky-100 flex items-center px-6 gap-4 sticky top-0 z-30">
                <button
                    @click="sidebarOpen = true"
                    class="lg:hidden text-sky-700 hover:text-sky-900"
                >
                    ☰
                </button>
                <div class="flex-1">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-6">
                <slot />
            </main>

        </div>
    </div>
</template>
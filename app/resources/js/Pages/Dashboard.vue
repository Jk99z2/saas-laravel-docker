<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-lg font-semibold text-sky-900">Dashboard</h1>
        </template>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100">
                <p class="text-sm text-sky-500 font-medium">Ventas hoy</p>
                <p class="text-3xl font-bold text-sky-900 mt-1">$0.00</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100">
                <p class="text-sm text-sky-500 font-medium">Ventas este mes</p>
                <p class="text-3xl font-bold text-sky-900 mt-1">$0.00</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100">
                <p class="text-sm text-sky-500 font-medium">Gastos este mes</p>
                <p class="text-3xl font-bold text-sky-900 mt-1">$0.00</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100">
                <p class="text-sm text-sky-500 font-medium">Utilidad</p>
                <p class="text-3xl font-bold text-sky-900 mt-1">$0.00</p>
            </div>
        </div>

        <!-- Placeholder para gráfica -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100">
            <p class="text-sm font-semibold text-sky-700 mb-4">Ventas por día</p>
            <div class="h-48 flex items-center justify-center text-sky-300 text-sm">
                Aquí irá la gráfica de ventas
            </div>
        </div>
    </AuthenticatedLayout>
</template>

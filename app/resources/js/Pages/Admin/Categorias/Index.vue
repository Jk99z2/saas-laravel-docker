<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    categorias: Array,
});

// Formulario nueva categoría
const form = useForm({
    nombre: '',
    descripcion: '',
});

// Formulario edición
const editForm = useForm({
    nombre: '',
    descripcion: '',
    activo: true,
});

const editando = ref(null);
const showForm = ref(false);

function submit() {
    form.post('/admin/categorias', {
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        }
    });
}

function startEdit(categoria) {
    editando.value = categoria.id;
    editForm.nombre = categoria.nombre;
    editForm.descripcion = categoria.descripcion ?? '';
    editForm.activo = categoria.activo;
}

function submitEdit(categoria) {
    editForm.put(`/admin/categorias/${categoria.id}`, {
        onSuccess: () => {
            editando.value = null;
        }
    });
}

function cancelEdit() {
    editando.value = null;
}

function destroy(categoria) {
    if (!confirm(`¿Eliminar la categoría "${categoria.nombre}"?`)) return;
    useForm({}).delete(`/admin/categorias/${categoria.id}`);
}
</script>

<template>
    <Head title="Categorías" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-lg font-semibold text-sky-900">Categorías</h1>
        </template>

        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Botón agregar -->
            <div class="flex justify-end">
                <button
                    @click="showForm = !showForm"
                    class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                >
                    {{ showForm ? 'Cancelar' : '+ Nueva categoría' }}
                </button>
            </div>

            <!-- Formulario nueva categoría -->
            <div v-if="showForm" class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100">
                <h2 class="text-sm font-semibold text-sky-800 mb-4">Nueva categoría</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-sky-700 mb-1">Nombre *</label>
                        <input
                            v-model="form.nombre"
                            type="text"
                            placeholder="Ej. Sabores frutales"
                            class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400"
                        />
                        <p v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">{{ form.errors.nombre }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-sky-700 mb-1">Descripción</label>
                        <input
                            v-model="form.descripcion"
                            type="text"
                            placeholder="Opcional"
                            class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400"
                        />
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-sky-600 hover:bg-sky-700 disabled:opacity-50 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                        >
                            Guardar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista de categorías -->
            <div class="bg-white rounded-2xl shadow-sm border border-sky-100 overflow-hidden">
                <div v-if="categorias.length === 0" class="p-8 text-center text-sky-300 text-sm">
                    No hay categorías aún. Crea la primera.
                </div>

                <div v-else>
                    <div
                        v-for="categoria in categorias"
                        :key="categoria.id"
                        class="border-b border-sky-50 last:border-0"
                    >
                        <!-- Vista normal -->
                        <div v-if="editando !== categoria.id" class="flex items-center justify-between px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-sky-900">{{ categoria.nombre }}</p>
                                <p class="text-xs text-sky-400 mt-0.5">{{ categoria.descripcion ?? 'Sin descripción' }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span
                                    :class="categoria.activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-500'"
                                    class="text-xs font-medium px-2 py-0.5 rounded-full"
                                >
                                    {{ categoria.activo ? 'Activa' : 'Inactiva' }}
                                </span>
                                <button @click="startEdit(categoria)" class="text-sky-400 hover:text-sky-700 text-xs">
                                    Editar
                                </button>
                                <button @click="destroy(categoria)" class="text-red-300 hover:text-red-500 text-xs">
                                    Eliminar
                                </button>
                            </div>
                        </div>

                        <!-- Vista edición inline -->
                        <div v-else class="px-6 py-4 bg-sky-50">
                            <form @submit.prevent="submitEdit(categoria)" class="space-y-3">
                                <div class="flex gap-3">
                                    <div class="flex-1">
                                        <input
                                            v-model="editForm.nombre"
                                            type="text"
                                            class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400"
                                        />
                                        <p v-if="editForm.errors.nombre" class="text-red-500 text-xs mt-1">{{ editForm.errors.nombre }}</p>
                                    </div>
                                    <div class="flex-1">
                                        <input
                                            v-model="editForm.descripcion"
                                            type="text"
                                            placeholder="Descripción"
                                            class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400"
                                        />
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-2 text-xs text-sky-700">
                                        <input type="checkbox" v-model="editForm.activo" class="rounded" />
                                        Activa
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="button" @click="cancelEdit" class="text-xs text-sky-400 hover:text-sky-700 px-3 py-1.5 rounded-lg border border-sky-200">
                                            Cancelar
                                        </button>
                                        <button type="submit" :disabled="editForm.processing" class="text-xs bg-sky-600 hover:bg-sky-700 disabled:opacity-50 text-white px-3 py-1.5 rounded-lg">
                                            Guardar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

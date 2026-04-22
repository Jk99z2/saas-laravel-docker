<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    productos: Array,
    categorias: Array,
});

const showForm = ref(false);
const editando = ref(null);

const form = useForm({
    nombre: '',
    categoria_id: '',
    descripcion: '',
    precio_venta: '',
    fotos: [],
});

const editForm = useForm({
    nombre: '',
    categoria_id: '',
    descripcion: '',
    precio_venta: '',
    activo: true,
    fotos: [],
});

function submit() {
    form.post('/admin/productos', {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        }
    });
}

function startEdit(producto) {
    editando.value = producto.id;
    editForm.nombre = producto.nombre;
    editForm.categoria_id = producto.categoria?.id ?? '';
    editForm.descripcion = producto.descripcion ?? '';
    editForm.precio_venta = producto.precio_venta;
    editForm.activo = producto.activo;
    editForm.fotos = [];
}

function submitEdit(producto) {
    editForm.post(`/admin/productos/${producto.id}?_method=PUT`, {
        forceFormData: true,
        onSuccess: () => {
            editando.value = null;
        }
    });
}

function cancelEdit() {
    editando.value = null;
}

function destroy(producto) {
    if (!confirm(`¿Eliminar "${producto.nombre}"?`)) return;
    useForm({}).delete(`/admin/productos/${producto.id}`);
}

function destroyFoto(producto, fotoId) {
    if (!confirm('¿Eliminar esta foto?')) return;
    useForm({ foto_id: fotoId }).delete(`/admin/productos/${producto.id}/fotos`);
}

function onFotosChange(e, target) {
    target.fotos = Array.from(e.target.files);
}

function formatPrice(val) {
    return Number(val).toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
}
</script>

<template>
    <Head title="Productos" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-lg font-semibold text-sky-900">Productos</h1>
        </template>

        <div class="max-w-5xl mx-auto space-y-6">

            <!-- Botón agregar -->
            <div class="flex justify-end">
                <button
                    @click="showForm = !showForm"
                    class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                >
                    {{ showForm ? 'Cancelar' : '+ Nuevo producto' }}
                </button>
            </div>

            <!-- Formulario nuevo producto -->
            <div v-if="showForm" class="bg-white rounded-2xl p-6 shadow-sm border border-sky-100">
                <h2 class="text-sm font-semibold text-sky-800 mb-4">Nuevo producto</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-sky-700 mb-1">Nombre *</label>
                            <input v-model="form.nombre" type="text" placeholder="Ej. Hielito de Mango"
                                class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400" />
                            <p v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">{{ form.errors.nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-sky-700 mb-1">Categoría *</label>
                            <select v-model="form.categoria_id"
                                class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400">
                                <option value="">Selecciona una categoría</option>
                                <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                            </select>
                            <p v-if="form.errors.categoria_id" class="text-red-500 text-xs mt-1">{{ form.errors.categoria_id }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-sky-700 mb-1">Precio de venta *</label>
                            <input v-model="form.precio_venta" type="number" step="0.01" min="0" placeholder="0.00"
                                class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400" />
                            <p v-if="form.errors.precio_venta" class="text-red-500 text-xs mt-1">{{ form.errors.precio_venta }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-sky-700 mb-1">Fotos</label>
                            <input type="file" multiple accept="image/*" @change="e => onFotosChange(e, form)"
                                class="w-full text-sm text-sky-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-sky-700 mb-1">Descripción</label>
                        <textarea v-model="form.descripcion" rows="2" placeholder="Descripción del producto"
                            class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400" />
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing"
                            class="bg-sky-600 hover:bg-sky-700 disabled:opacity-50 text-white text-sm font-medium px-4 py-2 rounded-lg">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista productos -->
            <div class="bg-white rounded-2xl shadow-sm border border-sky-100 overflow-hidden">
                <div v-if="productos.length === 0" class="p-8 text-center text-sky-300 text-sm">
                    No hay productos aún.
                </div>

                <div v-else>
                    <div v-for="producto in productos" :key="producto.id" class="border-b border-sky-50 last:border-0">

                        <!-- Vista normal -->
                        <div v-if="editando !== producto.id" class="flex items-center gap-4 px-6 py-4">
                            <!-- Foto -->
                            <div class="w-14 h-14 rounded-xl bg-sky-50 flex-shrink-0 overflow-hidden">
                                <img
                                    v-if="producto.foto_principal"
                                    :src="producto.foto_principal"
                                    class="w-14 h-14 object-cover aspect-square"
                                    style="height: 56px !important; width: 56px !important;"
                                />
                                <span v-else class="w-14 h-14 flex items-center justify-center text-2xl">🧊</span>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-sky-900">{{ producto.nombre }}</p>
                                <p class="text-xs text-sky-400">{{ producto.categoria?.nombre }}</p>
                            </div>

                            <!-- Precios -->
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold text-sky-900">{{ formatPrice(producto.precio_venta) }}</p>
                                <p class="text-xs text-green-500">{{ producto.margen_ganancia }}% margen</p>
                            </div>

                            <!-- Estado -->
                            <span :class="producto.activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-500'"
                                class="text-xs font-medium px-2 py-0.5 rounded-full hidden sm:block">
                                {{ producto.activo ? 'Activo' : 'Inactivo' }}
                            </span>

                            <!-- Acciones -->
                            <div class="flex gap-3">
                                <button @click="startEdit(producto)" class="text-sky-400 hover:text-sky-700 text-xs">Editar</button>
                                <button @click="destroy(producto)" class="text-red-300 hover:text-red-500 text-xs">Eliminar</button>
                            </div>
                        </div>

                        <!-- Vista edición -->
                        <div v-else class="px-6 py-4 bg-sky-50 space-y-4">
                            <form @submit.prevent="submitEdit(producto)" class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-sky-700 mb-1">Nombre</label>
                                        <input v-model="editForm.nombre" type="text"
                                            class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400" />
                                        <p v-if="editForm.errors.nombre" class="text-red-500 text-xs mt-1">{{ editForm.errors.nombre }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-sky-700 mb-1">Categoría</label>
                                        <select v-model="editForm.categoria_id"
                                            class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400">
                                            <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-sky-700 mb-1">Precio de venta</label>
                                        <input v-model="editForm.precio_venta" type="number" step="0.01" min="0"
                                            class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-sky-700 mb-1">Nuevas fotos</label>
                                        <input type="file" multiple accept="image/*" @change="e => onFotosChange(e, editForm)"
                                            class="w-full text-sm text-sky-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-sky-100 file:text-sky-700" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-sky-700 mb-1">Descripción</label>
                                    <textarea v-model="editForm.descripcion" rows="2"
                                        class="w-full border border-sky-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400" />
                                </div>

                                <!-- Fotos actuales -->
                                <div v-if="producto.fotos?.length" class="flex gap-2 flex-wrap">
                                    <div v-for="foto in producto.fotos" :key="foto.id" class="relative">
                                        <img :src="foto.url_r2" class="w-16 h-16 object-cover rounded-lg" />
                                        <button type="button" @click="destroyFoto(producto, foto.id)"
                                            class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center">
                                            ✕
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-2 text-xs text-sky-700">
                                        <input type="checkbox" v-model="editForm.activo" class="rounded" />
                                        Activo
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="button" @click="cancelEdit"
                                            class="text-xs text-sky-400 hover:text-sky-700 px-3 py-1.5 rounded-lg border border-sky-200">
                                            Cancelar
                                        </button>
                                        <button type="submit" :disabled="editForm.processing"
                                            class="text-xs bg-sky-600 hover:bg-sky-700 disabled:opacity-50 text-white px-3 py-1.5 rounded-lg">
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

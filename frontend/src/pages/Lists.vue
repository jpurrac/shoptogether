<template>
  <div class="antialiased bg-gray-50 dark:bg-gray-900 h-full">
    <div class="p-4 pt-20">
      <h1 class="text-2xl font-bold mb-4">Mis listas</h1>
      <div v-if="lists?.length" class="space-y-3 pb-15">
        <div
          v-long-press="{ handler: () => list.is_closed ? '' : OpenModalDeleteList(list), delay: 2000 }"
          v-for="list in lists"
          :key="list.id"
          @click="ChargeList(list.id, list)"
          class="
            relative flex items-center gap-3
            py-2 px-4 rounded-xl border shadow-sm
            transition-all duration-150
            hover:bg-gray-50 active:scale-[0.99]
            cursor-pointer
            border-brand-subtle bg-brand-softer/40
          "
          :class="{ 'opacity-75 border-gray-300 bg-gray-50': list.is_closed }"
        >
          <div class="flex w-full justify-between items-center pl-2">
            <!-- Izquierda -->
            <div class="flex flex-col gap-1">
              <span class="font-semibold text-heading">
                {{ list.name }}
              </span>

              <div class="flex items-center gap-2 text-xs text-gray-500">
                <span>{{ FormatUnixSecondsChile(list.created_at) }}</span>
                <!-- Badge cerrada -->
                <span
                  v-if="list.is_closed"
                  class="px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-700"
                >
                  Pagada
                </span>
                <!-- Badge role -->
                <span
                  v-if="list.role !== 'owner'"
                  class="px-2 py-0.5 rounded-full font-medium bg-purple-100 text-purple-700"
                >
                  Compartida
                </span>
              </div>
            </div>

            <!-- Derecha -->
            <div class="flex flex-col items-end gap-1 text-xs">
              <span class="flex items-center gap-1 text-gray-600">
                🛒 {{ list.items_count }} items
              </span>

              <span v-if="list.budget" class="font-medium text-heading">
                💰 {{ CLPFormat(list.budget) }}
              </span>
            </div>

          </div>
        </div>
      </div>
      <div v-else class="text-sm text-gray-500">
        No hay listas
      </div>

      <!-- FAB crear lista -->
      <div class="fixed bottom-3 left-3 right-3 z-50">
        <button
          @click="OpenModalList"
          class="
            w-full h-10 py-6 
            rounded-lg flex justify-center items-center border 
            bg-blue-600 text-white
            shadow-lg
            hover:bg-blue-700 active:scale-95
            transition
            hover:cursor-pointer
          "
        >
          Nueva Lista
        </button>
      </div>
    </div>


    <Modal
      v-model="modalList"
      closeable
      :title="'Nueva lista'"
    >
      <div class="max-w-md mx-auto space-y-6">

        <!-- Nombre -->
        <div>
          <label class="block mb-1.5 text-sm font-medium text-heading">
            Nombre de la lista
          </label>
          <input
            v-model="modalList.name"
            type="text"
            placeholder="Ej: Navidad"
            class="w-full border rounded-lg px-4 py-3 text-heading
                  focus:ring-2 focus:ring-brand-soft focus:border-brand"
          />
        </div>

        <!-- Cantidad + Precio -->
        <div class=" gap-4">

          <!-- Precio -->
          <div>
            <label class="block mb-1.5 text-sm font-medium text-heading">
              Presupuesto <span class="text-xs text-gray-400">(opcional)</span>
            </label>

            <input
              v-model="priceCLP"
              inputmode="numeric"
              placeholder="$ 0"
              class="w-full border rounded-lg px-4 py-3 text-heading
                    focus:ring-2 focus:ring-brand-soft focus:border-brand"
            />
          </div>
        </div>

        <!-- Botón acción -->
        <button
          @click="CreateList()"
          class="
            w-full py-3 rounded-xl font-semibold text-white
            bg-blue-600 hover:bg-blue-700
            focus:ring-4 focus:ring-blue-300
            transition
            hover:cursor-pointer
          "
        >
          {{ 'Agregar lista' }}
        </button>

      </div>
    </Modal>

  </div>
</template>
<script setup>
import { ref, inject, onMounted, computed } from 'vue';
import { vLongPress } from '../directives/longPress'
import { useUtils } from '../composable/useUtils';
import router from '../router';
import api from '../service/axiosInstance';


const { CLPFormat, FormatUnixSecondsChile } = useUtils();
const modalList = ref(null);
const modalDeleteList = ref(null);
const lists = ref(null);

const Alert = inject('ShowAlert');

onMounted(() => {
  GetLists();
});

const GetLists = () => {
  api.get(`/lists`)
  .then(res => {
    console.log(res.data);
    let response = res.data;
    if(!response.status_code == 200) return Alert({ msg: 'Error al cargar las listas', color: 'red' });
    lists.value = response.data;
    Alert({ msg: response.message , color: 'blue' });
  })
  .catch(err => {
    console.error('Error:', err)
  })
}

const CreateList = () => {
  if (!modalList.value) return;
  if(!modalList.value.name) return Alert({ msg : 'La lista debe tener un nombre', color : 'yellow'});
  api.post('/lists/create', modalList.value)
  .then(res => {
    let response = res.data; 
    if(response.status_code != 201) return Alert({ msg: 'Error al crear lista', color: 'red' });
      modalList.value = null;
      Alert({ msg : response.message, color : 'blue'});
      ChargeList(response.data.id);
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al crear la lista', color: 'red' });
  })
}

const priceCLP = computed({
  get() {
    if (!modalList.value) return '';
    if (modalList.value.budget == null) return '';
    return CLPFormat(modalList.value.budget);
  },
  set(value) {
    if (!modalList.value) return;
    const numeric = value.replace(/[^\d]/g, '');
    modalList.value.budget = numeric ? Number(numeric) : null;
  }
});

const ChargeList = (id) => {router.push(`/lists/${id}`);}
const OpenModalList = () => {modalList.value = {name : '', budget: null};}

const OpenModalDeleteList = (list) => {
  if(list.role != 'owner') return Alert({msg : 'Solo el propietario puede eliminar la lista.', color : 'yellow'});
  modalDeleteList.value = list
}

</script>
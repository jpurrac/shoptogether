<template>
  <div class="antialiased bg-gray-50 dark:bg-gray-900 h-full">
    <div class="p-4 pt-20" >
      <div class="flex flex-col items-start mb-3 xs:mb-3">
        <h1 class="text-2xl font-bold cursor-pointer"
            @click="list.is_closed ? '' : OpenModalDetailList()"
            v-if="list">
          {{ list.name }}
        </h1>
        <div class="flex justify-between w-full items-center">
          <span class="text-sm items-center" v-if="items">Productos {{ items.length }} / {{ items.filter(item=>item.checked).length }}</span>
          <div class="flex -space-x-4 rtl:space-x-reverse" @click="OpenModalSharedUsers">
            <img v-for="shared in shared_users.filter(item => item.id != user.id)" class="w-7 h-7 border border-buffer rounded-full" :src="shared.picture" alt="">
          </div>
        </div>

      </div>

      <div v-if="items.length" class="space-y-3 pb-28">
        <div
          v-for="item in items"
          :key="item.id"
          class="relative overflow-hidden rounded-xl"
        >

          <!-- 🔴 CAPA INFERIOR -->
          <div
          class="
            absolute inset-0
            flex items-center justify-start
            bg-red-500
            pl-6
          "
        >
          <span class="text-white font-semibold">
            Eliminar
          </span>
        </div>
          <!-- SWIPE -->
          <div
            :ref="el => setItemRef(el, item)"
            @click="!list.is_closed && OpenModalItem(item)"
            class="
              relative z-10
              flex items-center gap-3
              p-4
              rounded-xl
              border
              shadow-sm
              transition-transform duration-150
              cursor-pointer
              no-select
            "
            :class="item.checked
              ? 'border-green-300 bg-green-50'
              : 'border-gray-200 bg-white'"
          >

            <!-- Indicador lateral -->
            <div
              class="absolute left-0 top-0 h-full w-2 rounded-l-xl"
              :class="item.checked ? 'bg-green-500' : 'bg-gray-300'"
            ></div>

            <!-- Contenido -->
            <div class="flex w-full justify-between items-center pl-2">
              <div class="flex flex-col gap-0.5">
                <span class="font-semibold text-heading">
                  {{ item.name }}
                </span>
                <span class="text-xs text-gray-500">
                  Cantidad: {{ item.quantity }}
                </span>
              </div>

              <div class="flex flex-col items-end gap-1">
                <span class="text-sm font-semibold text-heading">
                  {{ CLPFormat(item.price * item.quantity) }}
                </span>
                <span class="text-xs text-gray-500">
                  {{ CLPFormat(item.price) }} c/u
                </span>
              </div>
            </div>

          </div>
        </div>


      </div>
      <div v-else>No hay items ingresados</div>
      <div
        v-if="list"
        class="fixed bottom-3 left-3 right-3 z-50"
      >
        <div
          :class="totalGraterThanBudget
          ? 'bg-red-300 border-red-400 text-red-800'
          : 'bg-white border-gray-100 text-gray-800'"
          class="rounded-2xl shadow-lg px-5 py-4 flex justify-between items-center border cursor-pointer"
          @click="OpenModalCloseBudget()"
        >
          <div>
            <p class="text-xs" >
              Total actual
            </p>
            <p class="text-xl font-semibold" >
              {{ CLPFormat(TotalListItems()) }}
            </p>
            <p class="text-xs" v-if="list.budget">
              {{ list.budget - TotalListItems() >= 0 ? '+' : '-' }}{{ CLPFormat(Math.abs(list.budget - TotalListItems())) }}
            </p>
          </div>
          <div class="flex justify-between items-center gap-5">
            <div class="text-right" v-if="list.budget">
              <p class="text-xs">Presupuesto</p>
              <p class="font-medium">{{ CLPFormat(list.budget) }}</p>
            </div>
            <div class="text-right" v-if="!list.is_closed">
              <button type="button" class="flex items-center justify-center text-white bg-blue-700 rounded-full w-14 h-14" @click.stop="OpenModalItem()">
                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7h-1M8 7h-.688M13 5v4m-2-2h4"/>
                </svg>
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>
    
    <Modal
      v-model="modalList"
      closeable
      :title="modalList && modalList.id ? 'Editar lista' : 'Nueva lista'"
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
              v-model="budgetCLP"
              inputmode="numeric"
              placeholder="$ 0"
              class="w-full border rounded-lg px-4 py-3 text-heading
                    focus:ring-2 focus:ring-brand-soft focus:border-brand"
            />
          </div>
        </div>

        <!-- Botón acción -->
        <button
          @click="EditList()"
          class="
            w-full py-3 rounded-xl font-semibold text-white
            bg-blue-600 hover:bg-blue-700
            focus:ring-4 focus:ring-blue-300
            transition
            hover:cursor-pointer
          "
        >
          {{ 'Guardar cambios' }}
        </button>

      </div>
    </Modal>
    <Modal v-model="modalDeleteList" closeable :title="'Eliminar lista'">

      <p class="text-center mb-5">¿Estas seguro de eliminar <span class="font-medium uppercase">{{ modalDeleteList.name }}</span>?</p>
      <button
        @click="DeleteList()"
        class="
          w-full py-3 rounded-xl font-semibold text-white
          bg-red-600 hover:bg-red-700
          focus:ring-4 focus:ring-red-300
          transition
        "
      >
        {{ 'Eliminar lista' }}
      </button>
    </Modal>
    <Modal
      v-model="modalCloseBudget"
      closeable
      :title="list?.is_closed ? 'Detalle de pago' : 'Pagar lista'"
    >
      <div class="max-w-md mx-auto space-y-6">
        <div class="bg-gray-50 rounded-xl p-4 space-y-2 shadow">
          <div class="flex justify-between text-sm">
            <span class="text-gray-500 font-bold">Presupuesto</span>
            <span class="font-medium">{{ CLPFormat(modalCloseBudget.budget) }}</span>
          </div>

          <div class="flex justify-between text-sm">
            <span class="text-gray-500 font-bold">Total ingresado</span>
            <span class="font-medium">{{ CLPFormat(modalCloseBudget.total) }}</span>
          </div>

          <div class="flex justify-between text-sm">
            <span class="text-gray-500 font-bold">Productos agregados</span>
            <span class="font-medium">{{ modalCloseBudget.countProductsChecked }}</span>
          </div>
        </div>

        <template v-if="list?.is_closed">
          <div class="bg-green-100 shadow rounded-xl p-4 space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 font-bold">Total pagado</span>
              <span class="font-semibold text-green-700">
                {{ CLPFormat(list.total_pay) }}
              </span>
            </div>

            <div class="flex justify-between text-sm">
              <span class="text-gray-600 font-bold">Método de pago</span>
              <span class="font-medium">
                {{ PAYMENT_METHODS[list.payment_method] }}
              </span>
            </div>

            <div v-if="list.payment_comment" class="text-sm text-gray-600">
              <span class="block text-sm text-gray-500 mb-1 font-bold">Comentario</span>
              {{ list.payment_comment }}
            </div>

            <div class="text-xs text-gray-500 pt-2 border-t">
              Pagado el {{ FormatUnixSecondsChile(list.closed_at) }}
            </div>
          </div>
        </template>

        <template v-else>
          <div>
            <label class="block mb-1.5 text-sm font-medium text-heading">
              Total a pagar
            </label>
            <input
              v-model="totalPayCLP"
              inputmode="numeric"
              placeholder="$ 0"
              class="w-full border rounded-xl px-4 py-3 text-heading
                    focus:ring-2 focus:ring-brand-soft focus:border-brand"
            />
          </div>

          <div>
            <label class="block mb-1.5 text-sm font-medium text-heading">
              Método de pago
            </label>
            <select
              v-model="modalCloseBudget.payment_method"
              class="w-full border rounded-xl px-4 py-3 text-heading
                    focus:ring-2 focus:ring-brand-soft focus:border-brand"
            >
              <option disabled value="">
                Selecciona un método
              </option>
              <option
                v-for="paymentMethod in modalCloseBudget.paymentMethod"
                :key="paymentMethod.value"
                :value="paymentMethod.value"
              >
                {{ paymentMethod.text }}
              </option>
            </select>
          </div>

          <div>
            <label class="block mb-1.5 text-sm font-medium text-heading">
              Comentario <span class="text-xs text-gray-400">(opcional)</span>
            </label>
            <input
              v-model="modalCloseBudget.payment_comment"
              type="text"
              placeholder="Ej: Pagado con débito"
              class="w-full border rounded-xl px-4 py-3 text-heading
                    focus:ring-2 focus:ring-brand-soft focus:border-brand"
            />
          </div>

          <button
            @click.prevent="CloseList()"
            class="
              w-full py-3 mt-4 rounded-xl font-semibold text-white
              bg-blue-600 hover:bg-blue-700
              focus:ring-4 focus:ring-blue-300
              transition
            "
          >
            Cerrar lista
          </button>

        </template>
      </div>
    </Modal>
    <Modal v-model="modalDetailList" closeable :title="'Detalle de lista'">

      <div class="rounded-xl  space-y-2">

        <div class="flex justify-between text-sm">
          <span class="">Creado por</span>
          <span class="font-medium">{{ list.created_by }}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="">Creado a las</span>
          <span class="font-medium">{{ FormatUnixSecondsChile(list.created_at) }}</span>
        </div>

        <div class="flex justify-between text-sm relative">
          <span>Código acceso</span>

          <span id="copy-share-code" class="font-medium flex items-center gap-2">
            {{ list.share_code }}

            <button
              @click="CopyShareCode"
              class="text-body hover:bg-neutral-quaternary rounded p-1.5 inline-flex items-center justify-center"
            >
              <!-- Icono normal -->
              <svg
                v-if="!copied"
                class="w-4 h-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-6 5h6m-6 4h6M10 3v4h4V3h-4Z"/>
              </svg>

              <!-- Icono success -->
              <svg
                v-else
                class="w-4 h-4 text-fg-brand"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4"/>
              </svg>
            </button>
          </span>
        </div>


        <div class="flex justify-between text-sm">
          <span class="">{{ list.budget ? 'Presupuesto' : 'Sin presupuesto asignado' }}</span>
          <span class="font-medium slashed-zero" v-if="list.budget">{{ CLPFormat(list.budget) }}</span>
        </div>
      </div>

      <button
        @click="OpenModalList(list)"
        class="
          w-full py-3 rounded-xl font-semibold text-white
          bg-blue-600 hover:bg-blue-700
          focus:ring-4 focus:ring-blue-300
          transition mt-10
        "
      >
        {{ 'Editar lista' }}
      </button>
      {{ list }}
      <button
        @click="OpenModalDeleteList(list)"
        class="
          w-full py-3 rounded-xl font-semibold text-white
          bg-red-600 hover:bg-red-700
          focus:ring-4 focus:ring-red-300
          transition mt-2
        "
      >
        {{ 'Eliminar lista' }}
      </button>
    </Modal>

    <Modal v-model="modalDeleteItem" closeable :title="'Eliminar producto'">

      <p class="text-center mb-5">¿Estas seguro de eliminar <span class="font-medium uppercase">{{ modalDeleteItem.name }}</span>?</p>
      <button
        @click="DeleteItem(list.id)"
        class="
          w-full py-3 rounded-xl font-semibold text-white
          bg-red-600 hover:bg-red-700
          focus:ring-4 focus:ring-red-300
          transition
        "
      >
        {{ 'Eliminar producto' }}
      </button>
    </Modal>
    <Modal
      v-model="modalSharedUsers"
      closeable
      title="Usuarios compartidos"
    >
      <div
        class="
          w-full
          overflow-y-auto
          px-1
          space-y-3
        "
      >

        <!-- Lista de usuarios -->
        <div
      v-for="user in shared_users"
      :key="user.id"
      class="
        flex items-center gap-3
        p-3 sm:p-4
        rounded-xl border
        bg-white
        hover:bg-gray-50
        transition
      "
    >
      <!-- Avatar -->
      <img
        :src="user.picture"
        :alt="user.name"
        class="
          w-10 h-10
          rounded-full
          object-cover
          shrink-0
        "
      />

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-heading truncate">
          {{ user.name }}
        </p>
      </div>

      <!-- Badge rol -->
      <span
        class="
          text-[11px]
          font-medium
          px-2 py-0.5
          rounded-full
          whitespace-nowrap
          flex items-center
        "
        :class="user.role === 'owner'
          ? 'bg-blue-100 text-blue-700'
          : 'bg-purple-100 text-purple-700'"
      >
        {{ user.role === 'owner' ? '👑 Propietario' : '✏️ Editor' }}
      </span>
        </div>

        <!-- Empty state -->
        <div
          v-if="!shared_users || !shared_users.length"
          class="text-center text-sm text-gray-500 py-6"
        >
          Esta lista no está compartida
        </div>

      </div>
    </Modal>
    <Modal
      v-model="modalItem"
      closeable
      :title="modalItem && modalItem.id ? 'Editar producto' : 'Nuevo producto'"
    >
      <div class="max-w-md mx-auto space-y-6">

        <!-- Nombre -->
        <div>
          <label class="block mb-1.5 text-sm font-medium text-heading">
            Nombre del producto
          </label>
          <input
            v-model="modalItem.name"
            type="text"
            placeholder="Ej: Leche descremada"
            class="w-full border rounded-lg px-4 py-3 text-heading
                  focus:ring-2 focus:ring-brand-soft focus:border-brand"
          />
        </div>

        <!-- Cantidad + Precio -->
        <div class="grid grid-cols-2 gap-4">

          <!-- Cantidad -->
          <div>
            <label class="block mb-1.5 text-sm font-medium text-heading">
              Cantidad
            </label>

            <div class="flex items-center border rounded-lg overflow-hidden">
              <button
                @click="ChangeQuantity('sub')"
                :disabled="modalItem.quantity < 2"
                class="px-4 py-3 disabled:opacity-40 disabled:cursor-not-allowed
                      hover:bg-gray-100 transition"
              >
                −
              </button>

              <input
                v-model="modalItem.quantity"
                readonly
                class="w-full text-center font-semibold text-heading border-x"
              />

              <button
                @click="ChangeQuantity('add')"
                class="px-4 py-3 hover:bg-gray-100 transition"
              >
                +
              </button>
            </div>
          </div>

          <!-- Precio -->
          <div>
            <label class="block mb-1.5 text-sm font-medium text-heading">
              Precio <span class="text-xs text-gray-400">(opcional)</span>
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

        <!-- Estado -->
        <div
          v-if="modalItem.id"
          class="flex items-center justify-between bg-gray-50 border rounded-lg px-4 py-3"
        >
          <div>
            <p class="text-sm font-medium text-heading">
              Estado
            </p>
            <p class="text-xs text-gray-500">
              {{ modalItem.checked ? 'Producto agregado al carrito' : 'Falta información' }}
            </p>
          </div>

          <label class="inline-flex items-center cursor-pointer">
            <input
              type="checkbox"
              v-model="modalItem.checked"
              class="sr-only peer"
            />
            <div
              class="w-11 h-6 rounded-full
                    bg-gray-400
                    peer-checked:bg-blue-600
                    relative transition-colors
                    after:content-['']
                    after:absolute after:top-[2px] after:left-[2px]
                    after:h-5 after:w-5 after:bg-white after:rounded-full
                    after:transition-transform
                    peer-checked:after:translate-x-full"
            ></div>
          </label>
        </div>

        <!-- Botón acción -->
        <button
          @click="modalItem && modalItem.id ? UpdateItem(list.id) : CreateItem(list.id)"
          class="
            w-full py-3 rounded-xl font-semibold text-white
            bg-blue-600 hover:bg-blue-700
            focus:ring-4 focus:ring-blue-300
            transition
          "
        >
          {{ modalItem && modalItem.id ? 'Guardar cambios' : 'Agregar producto' }}
        </button>

      </div>
    </Modal>
  </div>
</template> 
<script setup>
import { computed, inject, onMounted, ref } from 'vue';
import { useUtils } from '../composable/useUtils';
import api from '../service/axiosInstance';
import { vLongPress } from '../directives/longPress'
import { useRoute } from 'vue-router';
import router from '../router';
import { useSwipe } from '@vueuse/core'

const { 
  CLPFormat, 
  FormatUnixSecondsChile, 
  PAYMENT_METHODS 
  } 
  = useUtils();

const modalList = ref(null);
const modalItem = ref(null);
const modalDeleteItem = ref(null);
const modalDeleteList = ref(null);
const modalCloseBudget = ref(null);
const modalSharedUsers = ref(null);
const modalDetailList = ref(null);
const list = ref(null);
const items = ref([]);
const shared_users = ref([]);
const copied = ref(false);
const showTooltip = ref(false);

const user = JSON.parse(localStorage.getItem('user'));
const Alert = inject('ShowAlert');

onMounted(() => {
  GetList();
});

const OpenModalList = (list) => {
  modalList.value = {...list};
  modalDetailList.value = null;
}

const setItemRef = (el, item) => {
  if (!el) return

  const MAX_SWIPE = 120

  const { lengthX } = useSwipe(el, {
    threshold: 40,

    onSwipe() {
      if (list.value.is_closed) return;

      // 🚫 bloquear swipe a la derecha
      if (lengthX.value > 0) return;

      // 👉 swipe izquierda
      el.style.transform = `translateX(${Math.max(lengthX.value, MAX_SWIPE)}px)`;
    },

    onSwipeEnd() {
      if (list.value.is_closed) return;

      if (lengthX.value < -80) OpenModalDeleteItem(item);
  
      el.style.transform = 'translateX(0)';
    },
  })
}



const CopyShareCode = async () => {
    try {
      await navigator.clipboard.writeText(list.value.share_code)

      copied.value = true;
      showTooltip.value = true;
      Alert({msg : 'Código copiado', color : 'blue'});

      setTimeout(() => {
        copied.value = false;
        showTooltip.value = false;
      }, 1500)
    } catch (e) {
      console.error('No se pudo copiar', e)
    }
  }

const OpenModalSharedUsers = () => {modalSharedUsers.value = shared_users.value}

const EditList = () => {
  if(!modalList.value || !modalList.value.name || !modalList.value.id) return Alert({ msg: 'El nombre es obligatorio', color: 'yellow' });
  api.put(`/lists/update/${modalList.value.id}`, modalList.value)
  .then(res => {
    let response = res.data;
    if(response.status_code != 200) Alert({msg : 'Error al actualizar datos', color : 'red'});
      list.value = response.data;
      modalList.value = null;
      Alert({ msg: 'Lista actualizada', color: 'blue' });
    
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al actualizar la lista', color: 'red' });
  })
}

const OpenModalDetailList = () => {modalDetailList.value = list.value};

const DeleteList = () => {
  if (!modalDeleteList.value?.id) return;
  api.delete(`/lists/delete/${modalDeleteList.value.id}`)
  .then(res => {
    let response = res.data;
    if(response.status_code != 200) return Alert({msg : "Error al eliminar la lista", color : 'red'});
    modalDeleteList.value = null;
    Alert({msg : response.message, color : 'green'});
    router.push(`/lists`);
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al eliminar la lista', color: 'red' });
  })
}

const OpenModalDeleteList = (list) => {
  const user = JSON.parse(localStorage.getItem('user'));
  if(list.id_owner != user.id) return Alert({msg : 'Solo el propietario puede eliminar la lista.', color : 'yellow'});
  modalDeleteList.value = list;
  modalDetailList.value = null;
}

const OpenModalItem = (item = null) => {
  modalItem.value = {
    name : item?.name ?? '',
    price : item?.price ?? null,
    quantity : item?.quantity ?? 1,
  };
  
  if(item) {
    modalItem.value.checked = item.checked;
    modalItem.value.id = item.id;
  }
}

const CreateItem = (id_list) => {
  if(!modalItem.value || !modalItem.value.name) return Alert({ msg: 'El nombre es obligatorio', color: 'yellow' });
  api.post(`/listitem/create/${id_list}`, modalItem.value)
  .then(res => {
    let response = res.data;
    if(response.status_code != 201) return Alert({msg : 'Error al cargar los datos', color : 'red' });
    ResponseData(response.data);
    modalItem.value = null;
    Alert({msg : response.message, color : 'green'});
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al crear el producto', color: 'red' });
  })
}

const UpdateItem = (id_list) => {
  if(!modalItem.value || !modalItem.value.name) return Alert({ msg: 'El nombre es obligatorio', color: 'yellow' });
  api.put(`/listitem/update/${id_list}`, modalItem.value)
  .then(res => {
    let response = res.data;
    if(response.status_code != 200) return Alert({ msg : 'Error al cargar los datos', color : 'red'});
    ResponseData(response.data);
    modalItem.value = null;
    Alert({msg : response.message, color : 'green'});
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al actualizar el producto', color: 'red' });
  })
}

const DeleteItem = (id_list) => {
  if(!modalDeleteItem.value || !modalDeleteItem.value.id) return;
  api.delete(`/listitem/delete/${id_list}/${modalDeleteItem.value.id}`)
  .then(res => {
    let response = res.data;
    if(response.status_code != 200) return Alert({ msg : 'Error al cargar los datos', color : 'red'});
    ResponseData(response.data);
    modalDeleteItem.value = null;
    Alert({msg : response.message, color : 'green'});
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al eliminar el producto', color: 'red' });
  })
}

const TotalListItems = () =>
  items.value
    .filter(item => item.checked)
    .reduce((acc, item) => acc + item.price * item.quantity, 0)

const ResponseData = (data) => {
  items.value = data.items;
  shared_users.value = data.shared_with;
  list.value = data.list
}

const totalGraterThanBudget = computed(() => {
  if(!list.value.budget || list.value.budget == null) return false;
  return TotalListItems() > list.value.budget
})

const GetList = () => {

  if(!list.value || !list.value.id) Alert({ msg: 'Lista inválida', color: 'red' });
  const route = useRoute();
  const id = route.params.id;
  api.get(`/listitem/items/${id}`)
  .then(res => {
    let response = res.data;
    if(response.status_code != 200) return Alert({ msg: 'Error al cargar datos', color: 'red' });
      ResponseData(response.data);
      Alert({msg : response.message, color : 'blue'})
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al cargar la lista', color: 'red' });
  })
}

const ChangeQuantity = (operation) => {
  if(!modalItem.value) return;
  if(operation == 'add') modalItem.value.quantity += 1;
  else if(operation == 'sub' && modalItem.value.quantity > 1) modalItem.value.quantity -= 1;
}

const useCLPComputed = (modelRef, field) => {
  return computed({
    get() {
      const value = modelRef.value?.[field]
      if (value == null) return ''
      return CLPFormat(value)
    },
    set(input) {
      const numeric = input.replace(/[^\d]/g, '')
      modelRef.value[field] = numeric ? Number(numeric) : null
    }
  })
}

const OpenModalDeleteItem = (item) => modalDeleteItem.value = item;

const priceCLP = useCLPComputed(modalItem, 'price');
const budgetCLP = useCLPComputed(modalList, 'budget');
const totalPayCLP = useCLPComputed(modalCloseBudget, 'total_pay');

const OpenModalCloseBudget = () => {
  if(items.value.filter(item => item.checked).length < 1) return;
  modalCloseBudget.value = {
    budget : list.value.budget,
    total : TotalListItems(),
    countProductsChecked : items.value.filter(item => item.checked).length,
    total_pay : 0,
    paymentMethod : [{text:'Efectivo', value : 'CASH'}, {text:'Tarjeta de crédito', value : 'CREDIT_CARD'}, {text:'Tarjeta de débito', value : 'DEBIT_CARD'}, {text:'Transferencia', value : 'TRANSFER'}],
  };
}

const CloseList = () => {
  if(!modalCloseBudget.value || !modalCloseBudget.value.total_pay) return Alert({ msg: 'El total a pagar es obligatorio', color: 'yellow' });
  if(!modalCloseBudget.value.payment_method) return Alert({ msg: 'El método de pago es obligatorio', color: 'yellow' });

  let data = {
    id_list : list.value.id,
    total_pay : modalCloseBudget.value.total_pay,
    payment_method : modalCloseBudget.value.payment_method,
    payment_comment : modalCloseBudget.value.payment_comment
  }

  api.post(`/lists/close/${list.value.id}`, data)
  .then(res => {
    let response = res.data;
    if(!response.status_code == 200) return Alert({ msg: 'Error al cerrar la lista', color: 'red' });
    ResponseData(response.data);
    modalCloseBudget.value = null;
    Alert({ msg: response.message, color: 'blue' });
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al cerrar la lista', color: 'red' });
  });
}
</script>

<style scoped>
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] {
  -moz-appearance: textfield;
  appearance: textfield;
}

.no-select {
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  user-select: none;
}
</style>


<template>
  <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-100 shadow">
    <div class="flex flex-wrap justify-between items-center">
      <div class="flex justify-start items-center">

        <a href="/" class="flex items-center justify-between mr-4">
          <img
            src="../../public/soloicon.png"
            class="mr-3 h-8"
            alt="Flowbite Logo"
          />
          <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">ShopTogheter</span>
        </a>
      </div>
      <div class="flex items-center order-2">

        <!-- Notifications -->
        <button
          type="button"
          data-dropdown-toggle="notification-dropdown"
          class="p-2 mr-1 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
        >
          <span class="sr-only">View notifications</span>
          <!-- Bell icon -->
          <svg
            aria-hidden="true"
            class="w-6 h-6"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
            ></path>
          </svg>
        </button>
        <!-- Dropdown menu -->
        <div
          class="hidden overflow-hidden z-80 my-4 max-w-sm text-base list-none bg-white rounded divide-y divide-gray-100 shadow-lg dark:divide-gray-600 dark:bg-gray-700 "
          id="notification-dropdown"
        >
          <div
            class="block py-2 px-4 text-base font-medium text-center text-gray-700 bg-gray-50 dark:bg-gray-600 dark:text-gray-300"
          >
            Notifications
          </div>
          <div>
            <a
              href="#"
              class="flex py-3 px-4 border-b hover:bg-gray-100 dark:hover:bg-gray-600 dark:border-gray-600"
            >
              <div class="flex-shrink-0">
                <img
                  class="w-11 h-11 rounded-full"
                  src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png"
                  alt="Bonnie Green avatar"
                />
                <div
                  class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 rounded-full border border-white bg-primary-700 dark:border-gray-700"
                >
                  <svg
                    aria-hidden="true"
                    class="w-3 h-3 text-white"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z"
                    ></path>
                    <path
                      d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"
                    ></path>
                  </svg>
                </div>
              </div>
              <div class="pl-3 w-full">
                <div
                  class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400"
                >
                  New message from
                  <span class="font-semibold text-gray-900 dark:text-white"
                    >Bonnie Green</span
                  >: "Hey, what's up? All set for the presentation?"
                </div>
                <div
                  class="text-xs font-medium text-primary-600 dark:text-primary-500"
                >
                  a few moments ago
                </div>
              </div>
            </a>
            <a
              href="#"
              class="flex py-3 px-4 border-b hover:bg-gray-100 dark:hover:bg-gray-600 dark:border-gray-600"
            >
              <div class="flex-shrink-0">
                <img
                  class="w-11 h-11 rounded-full"
                  src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png"
                  alt="Jese Leos avatar"
                />
                <div
                  class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 bg-gray-900 rounded-full border border-white dark:border-gray-700"
                >
                  <svg
                    aria-hidden="true"
                    class="w-3 h-3 text-white"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                    ></path>
                  </svg>
                </div>
              </div>
              <div class="pl-3 w-full">
                <div
                  class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400"
                >
                  <span class="font-semibold text-gray-900 dark:text-white"
                    >Jese leos</span
                  >
                  and
                  <span class="font-medium text-gray-900 dark:text-white"
                    >5 others</span
                  >
                  started following you.
                </div>
                <div
                  class="text-xs font-medium text-primary-600 dark:text-primary-500"
                >
                  10 minutes ago
                </div>
              </div>
            </a>
            <a
              href="#"
              class="flex py-3 px-4 border-b hover:bg-gray-100 dark:hover:bg-gray-600 dark:border-gray-600"
            >
              <div class="flex-shrink-0">
                <img
                  class="w-11 h-11 rounded-full"
                  src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/joseph-mcfall.png"
                  alt="Joseph McFall avatar"
                />
                <div
                  class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 bg-red-600 rounded-full border border-white dark:border-gray-700"
                >
                  <svg
                    aria-hidden="true"
                    class="w-3 h-3 text-white"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                      clip-rule="evenodd"
                    ></path>
                  </svg>
                </div>
              </div>
              <div class="pl-3 w-full">
                <div
                  class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400"
                >
                  <span class="font-semibold text-gray-900 dark:text-white"
                    >Joseph Mcfall</span
                  >
                  and
                  <span class="font-medium text-gray-900 dark:text-white"
                    >141 others</span
                  >
                  love your story. See it and view more stories.
                </div>
                <div
                  class="text-xs font-medium text-primary-600 dark:text-primary-500"
                >
                  44 minutes ago
                </div>
              </div>
            </a>
            <a
              href="#"
              class="flex py-3 px-4 border-b hover:bg-gray-100 dark:hover:bg-gray-600 dark:border-gray-600"
            >
              <div class="flex-shrink-0">
                <img
                  class="w-11 h-11 rounded-full"
                  src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/roberta-casas.png"
                  alt="Roberta Casas image"
                />
                <div
                  class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 bg-green-400 rounded-full border border-white dark:border-gray-700"
                >
                  <svg
                    aria-hidden="true"
                    class="w-3 h-3 text-white"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z"
                      clip-rule="evenodd"
                    ></path>
                  </svg>
                </div>
              </div>
              <div class="pl-3 w-full">
                <div
                  class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400"
                >
                  <span class="font-semibold text-gray-900 dark:text-white"
                    >Leslie Livingston</span
                  >
                  mentioned you in a comment:
                  <span
                    class="font-medium text-primary-600 dark:text-primary-500"
                    >@bonnie.green</span
                  >
                  what do you say?
                </div>
                <div
                  class="text-xs font-medium text-primary-600 dark:text-primary-500"
                >
                  1 hour ago
                </div>
              </div>
            </a>
            <a
              href="#"
              class="flex py-3 px-4 hover:bg-gray-100 dark:hover:bg-gray-600"
            >
              <div class="flex-shrink-0">
                <img
                  class="w-11 h-11 rounded-full"
                  src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/robert-brown.png"
                  alt="Robert image"
                />
                <div
                  class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 bg-purple-500 rounded-full border border-white dark:border-gray-700"
                >
                  <svg
                    aria-hidden="true"
                    class="w-3 h-3 text-white"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                    ></path>
                  </svg>
                </div>
              </div>
              <div class="pl-3 w-full">
                <div
                  class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400"
                >
                  <span class="font-semibold text-gray-900 dark:text-white"
                    >Robert Brown</span
                  >
                  posted a new video: Glassmorphism - learn how to implement
                  the new design trend.
                </div>
                <div
                  class="text-xs font-medium text-primary-600 dark:text-primary-500"
                >
                  3 hours ago
                </div>
              </div>
            </a>
          </div>
          <a
            href="#"
            class="block py-2 text-md font-medium text-center text-gray-900 bg-gray-50 hover:bg-gray-100 dark:bg-gray-600 dark:text-white dark:hover:underline"
          >
            <div class="inline-flex items-center">
              <svg
                aria-hidden="true"
                class="mr-2 w-4 h-4 text-gray-500 dark:text-gray-400"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                <path
                  fill-rule="evenodd"
                  d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                  clip-rule="evenodd"
                ></path>
              </svg>
              View all
            </div>
          </a>
        </div>
        <!-- Apps -->


        <button
          type="button"
          class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
          id="user-menu-button"
          aria-expanded="false"
          data-dropdown-toggle="dropdown"
        >
          <span class="sr-only">Open user menu</span>
          <img
            class="w-8 h-8 rounded-full"
            :src="userImage"
            alt="user photo"
          />
        </button>
        <!-- Dropdown menu -->
        <div
          class="hidden z-50 my-4 w-56 text-base list-none bg-white divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
          id="dropdown"
        >
        <div class="p-2">
          <div class="py-3 px-4 rounded-lg bg-gray-100">
            <span
              class="block text-sm font-semibold text-gray-900 dark:text-white"
              >{{ user.name }}</span
            >
            <span
              class="block text-sm text-gray-900 truncate dark:text-white"
              >{{user.email}}</span
            >
          </div>
        </div>

          <ul
            class="py-1 text-gray-700 dark:text-gray-300"
            aria-labelledby="dropdown"
          >
            <li @click="OpenModalShareCode()" v-if="route.name == 'lists'">
              <a
                href="#"
                class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"

                >Enlazar a lista</a
              >
            </li>
            <li>
              <a
                href="#"
                class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                @click="emit('Logout');"
                >Cerrar sesión</a
              >
            </li>
          </ul>
        </div>
      </div>
      <Modal
        v-model="modalShareCode"
        closeable
        title="Enlazar lista"
      >
      <div class="w-full overflow-y-auto px-1 space-y-3">

        <div>
          <label class="block mb-1.5 text-sm font-medium text-heading">
            Código de lista
          </label>
          <input
            ref="shareInput"
            v-model="modalShareCode.share_code"
            @keyup.enter="ShareCode"
            maxlength="5"
            type="text"
            class="w-full border rounded-lg px-4 py-3 text-heading
                  focus:ring-2 focus:ring-brand-soft focus:border-brand text-center font-bold"
          />

        </div>
        <button
          @click="ShareCode()"
          class="
            w-full py-3 rounded-xl font-semibold text-white
            bg-blue-600 hover:bg-blue-700
            focus:ring-4 focus:ring-blue-300
            transition
            hover:cursor-pointer
          "
        >
          {{ 'Enlazar a lista' }}
        </button>
      </div>
    </Modal>
    </div>
  </nav>
</template>
<script setup>
import { computed, inject, ref } from 'vue';
import { useRoute } from 'vue-router';
import api from '../service/axiosInstance';
import router from '../router';
const route = useRoute();
const modalShareCode = ref(null);
const emit = defineEmits(['Logout']);
const user = JSON.parse(localStorage.getItem('user'));
const Alert = inject('ShowAlert');
const OpenModalShareCode = () => {modalShareCode.value = {'share_code' : ''}};

const ShareCode = () => {
  if (!modalShareCode.value) return;
  if(!modalShareCode.value.share_code) return Alert({ msg : 'Debes ingresar el código de una lista', color : 'yellow'});
  api.post('/lists/share', modalShareCode.value)
  .then(res => {
    let response = res.data; 
    if(response.status_code != 200) return Alert({ msg: 'Error al aisgnar lista', color: 'red' });
      modalShareCode.value = null;
      Alert({ msg : response.message, color : 'blue'});
      router.push(`/lists/${response.data.list_id}`);
  })
  .catch(err => {
    console.error('Error:', err);
    Alert({ msg: 'Error al crear la lista', color: 'red' });
  })
}

const userImage = computed(() => {
  if(user && !user.picture) return 'https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png';
  if(user && user.picture) {
    if(user.google_id) return user.picture;
    else return `https://backend.jpurrac.cl${user.picture}`;
  }

});
</script>
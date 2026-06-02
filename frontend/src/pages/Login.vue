  <template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100 px-4">
      <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl p-8 space-y-4">

        <!-- Logo -->
        <div class="flex flex-col justify-center items-center">
          <img src="/soloicon.png" alt="logo" class="w-20 h-20" />
          <h1 class="">ShopTogether</h1>
        </div>

        <div>
          <label class="block mb-1.5 text-sm font-medium text-heading">Usuario</label>
          <div class="relative">
            <input
              v-model="modalLogin.email"
              type="email"
              placeholder="correo@email.com"
              class="w-full border rounded-xl px-4 py-3 pl-10 focus:ring-2 focus:ring-brand-soft focus:border-brand"
            />
            <span class="absolute left-3 top-3.5 text-gray-400">👤</span>
          </div>
        </div>

        <!-- Password -->
        <div>
          <label class="block mb-1.5 text-sm font-medium text-heading">Contraseña</label>
          <div class="relative">
            <input
              v-model="modalLogin.password"
              type="password"
              placeholder="••••••••"
              class="w-full border rounded-xl px-4 py-3 pl-10 focus:ring-2 focus:ring-brand-soft focus:border-brand"
            />
            <span class="absolute left-3 top-3.5 text-gray-400">🔒</span>
          </div>
        </div>

        <!-- Botón principal -->
        <button
          class="w-full py-3 rounded-xl font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition hover:cursor-pointer"
          @click="DoLoging()"
        >
          Iniciar sesión
        </button>

        <!-- 🆕 Crear cuenta -->
        <div class="text-center">
          <p class="text-sm text-gray-500">
            ¿No tienes cuenta?
            <button
              @click="modalCreateUser = { name : '', email : '', password : '' , confirmPassword : '' , picture : '' }"
              class="font-medium text-blue-600 hover:text-blue-700 hover:underline ml-1"
            >
              Crear usuario
            </button>
          </p>
        </div>

        <!-- Divider -->
        <div class="flex items-center gap-3">
          <div class="flex-1 h-px bg-gray-200"></div>
          <span class="text-xs text-gray-400">o continúa con</span>
          <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <!-- Google Button -->
        <div id="google-btn" class="flex justify-center"></div>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-400">© 2026 ShopTogether</p>
      </div>
      <Modal v-model="modalCreateUser" title="Crear cuenta">

          <div class="w-full max-w-sm bg-white  space-y-2">
            <!-- Imagen perfil -->
            <div class="flex flex-col items-center gap-3">
              <div class="w-24 h-24 rounded-full overflow-hidden border bg-gray-100 flex items-center justify-center">
                <img v-if="imagePreview" :src="imagePreview" class="object-cover w-full h-full" />
                <span v-else class="text-3xl text-gray-400">👤</span>
              </div>

              <label class="text-sm text-blue-600 cursor-pointer hover:underline">
                Cambiar foto
                <input type="file" class="hidden" accept="image/*" @change="handleImage" />
              </label>
            </div>

            <!-- Nombre -->
            <div>
              <label class="block mb-1.5 text-sm font-medium text-heading">Nombre</label>
              <input
                v-model="modalCreateUser.name"
                type="text"
                placeholder="Ej: Juan Pérez"
                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-soft focus:border-brand"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block mb-1.5 text-sm font-medium text-heading">Email</label>
              <input
                v-model="modalCreateUser.email"
                type="email"
                placeholder="correo@email.com"
                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-soft focus:border-brand"
              />
            </div>

            <!-- Password -->
            <div>
              <label class="block mb-1.5 text-sm font-medium text-heading">Contraseña</label>
              <input
                v-model="modalCreateUser.password"
                type="password"
                placeholder="••••••••"
                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-soft focus:border-brand"
              />
            </div>

            <!-- Confirm Password -->
            <div>
              <label class="block mb-1.5 text-sm font-medium text-heading">Confirmar contraseña</label>
              <input
                v-model="modalCreateUser.confirmPassword"
                type="password"
                placeholder="••••••••"
                class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-soft focus:border-brand"
                :class="modalCreateUser.confirmPassword && !passwordsMatch ? 'border-red-400' : ''"
              />
              <p v-if="modalCreateUser.confirmPassword && !passwordsMatch" class="text-xs text-red-500 mt-1">
                Las contraseñas no coinciden
              </p>
            </div>

            <!-- Botón -->
            <button
              @click="CreateUser()"
              :disabled="!passwordsMatch || !modalCreateUser.name || !modalCreateUser.email"
              class="w-full py-3 rounded-xl font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 transition mt-5 hover:cursor-pointer"
            >
              Crear cuenta
            </button>

          </div>

      </Modal>
    </div>
  </template>
  <script setup>
  import { onMounted, onBeforeUnmount, inject, ref, computed } from 'vue'
  import api from '../service/axiosInstance'

  const CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID;
  const modalLogin = ref({email : '' , password : ''});
  const modalCreateUser = ref(null);
  const SetUser = inject('SetUser');
  const imagePreview = ref(null)
  const Alert = inject('ShowAlert');

  const passwordsMatch = computed(() =>
    modalCreateUser.value.password && modalCreateUser.value.password === modalCreateUser.value.confirmPassword
  )

  const CreateUser = () => {
    const formData = new FormData()
    formData.append('name', modalCreateUser.value.name || '')
    formData.append('email', modalCreateUser.value.email || '')
    formData.append('password', modalCreateUser.value.password || '')
    formData.append('confirmPassword', modalCreateUser.value.confirmPassword || '')

    if (modalCreateUser.value.image) {
      formData.append('image', modalCreateUser.value.image)
    }
    api.post('/auth/register', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    .then(res => {
      let response = res.data;
      if(response.status_code != 201) return Alert({msg: 'Error al crear usuario', color: 'red'});
      modalCreateUser.value = null;
      Alert({msg: response.message, color: 'green'});
    }).catch(err => {
      console.error('Error al crear usuario:', err)
      Alert({msg: 'Error al crear usuario', color: 'red'});
    })
  }

  const DoLoging = () => {
    if(!modalLogin.value.email && !modalLogin.value.password) return Alert({msg : 'Ingrese los datos de inicio de sesión',color : 'yellow'});
    if(!modalLogin.value.email) return Alert({msg : 'El correo es requerido',color : 'yellow'});
    if(!modalLogin.value.password) return Alert({msg :'La contraseña es requerida', color :'yellow'});

    api.post('/auth/login', modalLogin.value)
    .then(res => {
      let response = res.data;
      if(response.status_code != 201) return Alert({msg : 'Error al iniciar sesion', color : 'red'});
      SetUser(response.data.user);
      localStorage.setItem('token', response.data.access_token);
      window.location.href = '/lists';
      Alert({msg : response.message, color : 'green'});
    }).catch(err => {
      let error = err.response.data;
      // console.error('Error autenticación:', error);
      Alert({msg: error.message, color:'red'});
      modalLogin.value = {email : '' , password : ''};
    })
  }

  function handleImage(e) {
    const file = e.target.files[0]
    if (!file) return
    modalCreateUser.value.image = file
    imagePreview.value = URL.createObjectURL(file)
  }

  function InitGoogle() {
    const el = document.getElementById('google-btn');
    if (!el) return;

    el.innerHTML = ''; // 🔑 CLAVE para logout/login

    google.accounts.id.initialize({
      client_id: CLIENT_ID,
      callback: HandleCredentialResponse,
      ux_mode: 'popup',
    });

    google.accounts.id.renderButton(el, {
      theme: 'filled_blue',
      size: 'large',
      width: 260,
      logo_alignment : 'center',
      locale: 'es',
      shape : 'pill',
      text : 'continue_with'
    });
  }

  onMounted(() => {
    InitGoogle();
  });

  onBeforeUnmount(() => {
    if (window.google?.accounts?.id) {
      google.accounts.id.cancel();
    }
  });

  function HandleCredentialResponse(response) {
    api.post('/auth/google', {
      token: response.credential,
    })
    .then(res => {
      let response = res.data.data;
      SetUser(response.user);
      localStorage.setItem('token', response.access_token);
      window.location.href = '/lists';
    })
    .catch(err => {
      console.error('Error autenticación:', err)
    })
  }
  </script>


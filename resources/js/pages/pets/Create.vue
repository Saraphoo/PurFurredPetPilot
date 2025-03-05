<script lang="ts">
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from "@/layouts/AppLayout.vue";

export default {
    components: {AppLayout},
    setup() {
        const form = useForm({
            name: '',
            sex: '',
            DOB: '',
            type: '',
            breed: ''
        });

        const submit = () => {
            form.post(route('pets.store'), {
                onSuccess: () => {
                    debugger;
                    form.reset()
                }
            });
        };

        return { form, submit };
    },
    props: {
        errors: Object,
        flash: Object
    }
};
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
    <div>
        <h1>Create a New Pet</h1>
        <form @submit.prevent="submit">
            <div>
                <label for="name">Name</label>
                <input v-model="form.name" type="text" id="name" />
                <span v-if="form.errors.name">{{ form.errors.name }}</span>
            </div>

            <div>
                <label for="sex">Gender</label>
                <input v-model="form.sex" type="text" id="sex" />
                <span v-if="form.errors.sex">{{ form.errors.sex }}</span>
            </div>

            <div>
                <label for="DOB">Date of Birth</label>
                <input v-model="form.DOB" type="date" id="DOB" />
                <span v-if="form.errors.DOB">{{ form.errors.DOB }}</span>
            </div>

            <div>
                <label for="type">Type (e.g. Dog, Cat)</label>
                <input v-model="form.type" type="text" id="type" />
                <span v-if="form.errors.type">{{ form.errors.type }}</span>
            </div>

            <div>
                <label for="species">Species/Breed</label>
                <input v-model="form.breed" type="text" id="species" />
                <span v-if="form.errors.breed">{{ form.errors.breed }}</span>
            </div>

            <button type="submit">Create Pet</button>
        </form>
    </div>
    </AppLayout>
</template>



<script lang="ts">
import { useForm } from '@inertiajs/inertia-vue3';

export default {
    setup() {
        const form = useForm({
            name: '',
            age: '',
            type: '',
            species: ''
        });

        const submit = () => {
            form.post('/pets', {
                onSuccess: () => form.reset()
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
    <div>
        <h1>Create a New Pet</h1>
        <form @submit.prevent="submit">
            <div>
                <label for="name">Name</label>
                <input v-model="form.name" type="text" id="name" />
                <span v-if="errors.name">{{ errors.name }}</span>
            </div>

            <div>
                <label for="age">Age</label>
                <input v-model="form.age" type="number" id="age" />
                <span v-if="errors.age">{{ errors.age }}</span>
            </div>

            <div>
                <label for="type">Type (e.g. Dog, Cat)</label>
                <input v-model="form.type" type="text" id="type" />
                <span v-if="errors.type">{{ errors.type }}</span>
            </div>

            <div>
                <label for="species">Species/Breed</label>
                <input v-model="form.species" type="text" id="species" />
                <span v-if="errors.species">{{ errors.species }}</span>
            </div>

            <button type="submit">Create Pet</button>
        </form>

        <p v-if="flash.success">{{ flash.success }}</p>
    </div>
</template>



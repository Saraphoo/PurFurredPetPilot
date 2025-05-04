import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import MedicalForm from '@/components/form/MedicalForm.vue';
import { useForm } from '@inertiajs/vue3';

// Mock Inertia's useForm
vi.mock('@inertiajs/vue3', () => ({
    useForm: vi.fn(() => ({
        post: vi.fn(),
        processing: false,
        errors: {}
    }))
}));

describe('MedicalForm', () => {
    let wrapper: any;

    beforeEach(() => {
        wrapper = mount(MedicalForm, {
            props: {
                petId: 1,
                initialData: {
                    special_needs: [
                        {
                            name: 'Diabetes',
                            affects: 'Diet',
                            notes: 'Requires insulin'
                        }
                    ],
                    medications: [
                        {
                            name: 'Insulin',
                            prescribed_on: '2024-01-01',
                            notes: 'Daily injection'
                        }
                    ],
                    notes: 'General medical notes'
                }
            },
            global: {
                plugins: [createTestingPinia()]
            }
        });
    });

    it('renders the form with initial data', () => {
        expect(wrapper.find('form').exists()).toBe(true);
        expect(wrapper.find('.v-card-title').text()).toBe('Special Needs');
    });

    it('adds a new special need when clicking add button', async () => {
        const addButton = wrapper.find('button:contains("Add Another Special Need")');
        await addButton.trigger('click');
        
        const specialNeeds = wrapper.findAll('.v-row').filter(row => 
            row.find('.v-autocomplete[label="Special Need Name"]').exists()
        );
        
        expect(specialNeeds.length).toBe(2);
    });

    it('removes a special need when clicking delete button', async () => {
        // First add a second special need
        const addButton = wrapper.find('button:contains("Add Another Special Need")');
        await addButton.trigger('click');
        
        // Then remove it
        const deleteButton = wrapper.find('button:contains("mdi-delete")');
        await deleteButton.trigger('click');
        
        const specialNeeds = wrapper.findAll('.v-row').filter(row => 
            row.find('.v-autocomplete[label="Special Need Name"]').exists()
        );
        
        expect(specialNeeds.length).toBe(1);
    });

    it('submits the form with correct data', async () => {
        const form = wrapper.find('form');
        await form.trigger('submit.prevent');

        expect(useForm).toHaveBeenCalledWith({
            pet_id: 1,
            special_needs: [
                {
                    name: 'Diabetes',
                    affects: 'Diet',
                    notes: 'Requires insulin'
                }
            ],
            medications: [
                {
                    name: 'Insulin',
                    prescribed_on: '2024-01-01',
                    notes: 'Daily injection'
                }
            ],
            notes: 'General medical notes'
        });
    });

    it('validates required fields', async () => {
        // Clear all required fields
        await wrapper.setData({
            specialNeeds: [{
                name: '',
                affects: '',
                notes: ''
            }],
            medications: [{
                name: '',
                prescribed_on: '',
                notes: ''
            }]
        });

        const form = wrapper.find('form');
        await form.trigger('submit.prevent');

        // Check if validation errors are shown
        expect(wrapper.findAll('.v-messages__message').length).toBeGreaterThan(0);
    });

    it('resets the form after successful submission', async () => {
        const form = wrapper.find('form');
        await form.trigger('submit.prevent');

        // Mock the onSuccess callback
        const resetSpy = vi.spyOn(wrapper.vm, 'reset');
        await wrapper.vm.reset();

        expect(resetSpy).toHaveBeenCalled();
        expect(wrapper.vm.specialNeeds).toEqual([{
            name: '',
            affects: '',
            notes: ''
        }]);
        expect(wrapper.vm.medications).toEqual([{
            name: '',
            prescribed_on: '',
            notes: ''
        }]);
    });
}); 
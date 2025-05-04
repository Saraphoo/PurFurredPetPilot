import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import HousingForm from '@/components/form/HousingForm.vue';
import { useForm } from '@inertiajs/vue3';

// Mock Inertia's useForm
vi.mock('@inertiajs/vue3', () => ({
    useForm: vi.fn(() => ({
        post: vi.fn(),
        processing: false,
        errors: {}
    }))
}));

describe('HousingForm', () => {
    let wrapper: any;

    beforeEach(() => {
        wrapper = mount(HousingForm, {
            props: {
                petId: 1,
                initialData: {
                    total_space_value: '100',
                    total_space_unit: 'square feet',
                    housing_type: 'Cage',
                    flooring_type: 'Wire',
                    bedding_type: 'Wood Shavings',
                    accessories: [
                        {
                            type: 'House',
                            name: 'Cozy Cave',
                            size: 'Large',
                            brand: 'Kaytee',
                            material: 'Plastic',
                            notes: 'Main sleeping area'
                        }
                    ],
                    notes: 'General housing notes'
                }
            },
            global: {
                plugins: [createTestingPinia()]
            }
        });
    });

    it('renders the form with initial data', () => {
        expect(wrapper.find('form').exists()).toBe(true);
        expect(wrapper.find('.v-card-title').text()).toBe('Daily Living Space');
    });

    it('adds a new accessory when clicking add button', async () => {
        const addButton = wrapper.find('button:contains("Add Another Accessory")');
        await addButton.trigger('click');
        
        const accessories = wrapper.findAll('.v-row').filter(row => 
            row.find('.v-autocomplete[label="Accessory Type"]').exists()
        );
        
        expect(accessories.length).toBe(2);
    });

    it('removes an accessory when clicking delete button', async () => {
        // First add a second accessory
        const addButton = wrapper.find('button:contains("Add Another Accessory")');
        await addButton.trigger('click');
        
        // Then remove it
        const deleteButton = wrapper.find('button:contains("mdi-delete")');
        await deleteButton.trigger('click');
        
        const accessories = wrapper.findAll('.v-row').filter(row => 
            row.find('.v-autocomplete[label="Accessory Type"]').exists()
        );
        
        expect(accessories.length).toBe(1);
    });

    it('submits the form with correct data', async () => {
        const form = wrapper.find('form');
        await form.trigger('submit.prevent');

        expect(useForm).toHaveBeenCalledWith({
            pet_id: 1,
            total_space_value: '100',
            total_space_unit: 'square feet',
            housing_type: 'Cage',
            flooring_type: 'Wire',
            bedding_type: 'Wood Shavings',
            accessories: [
                {
                    type: 'House',
                    name: 'Cozy Cave',
                    size: 'Large',
                    brand: 'Kaytee',
                    material: 'Plastic',
                    notes: 'Main sleeping area'
                }
            ],
            notes: 'General housing notes'
        });
    });

    it('validates required fields', async () => {
        // Clear all required fields
        await wrapper.setData({
            totalSpaceValue: '',
            totalSpaceUnit: '',
            housingType: '',
            flooringType: '',
            beddingType: ''
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
        expect(wrapper.vm.totalSpaceValue).toBe('');
        expect(wrapper.vm.totalSpaceUnit).toBe('');
        expect(wrapper.vm.housingType).toBe('');
        expect(wrapper.vm.flooringType).toBe('');
        expect(wrapper.vm.beddingType).toBe('');
        expect(wrapper.vm.accessories).toEqual([{
            type: '',
            name: '',
            size: '',
            brand: '',
            material: '',
            notes: ''
        }]);
    });
}); 
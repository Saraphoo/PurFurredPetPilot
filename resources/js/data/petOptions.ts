export type PetType = 'dog' | 'cat' | 'bird' | 'reptile' | 'amphibian' | 'rodent' | 'rabbit' | 'fish' | 'invertebrate' | 'other';

export const petTypes = [
    { label: 'Dog', value: 'dog' },
    { label: 'Cat', value: 'cat' },
    { label: 'Bird', value: 'bird' },
    { label: 'Reptile', value: 'reptile' },
    { label: 'Amphibian', value: 'amphibian' },
    { label: 'Rodent', value: 'rodent' },
    { label: 'Rabbit', value: 'rabbit' },
    { label: 'Fish', value: 'fish' },
    { label: 'Invertebrate', value: 'invertebrate' },
    { label: 'Other', value: 'other' },
] as const;

export const speciesOptions: Record<PetType, string[]> = {
    dog: [
        'Affenpinscher', 'Afghan Hound', 'Airedale Terrier', 'Akita', 'Alaskan Malamute', 'American Bulldog',
        'American Cocker Spaniel', 'American Staffordshire Terrier', 'Australian Cattle Dog', 'Australian Shepherd',
        'Basenji', 'Basset Hound', 'Beagle', 'Bernese Mountain Dog', 'Bichon Frise', 'Border Collie',
        'Border Terrier', 'Boston Terrier', 'Boxer', 'Bulldog', 'Bullmastiff', 'Cavalier King Charles Spaniel',
        'Chihuahua', 'Chow Chow', 'Cocker Spaniel', 'Collie', 'Corgi', 'Dachshund', 'Dalmatian',
        'Doberman Pinscher', 'English Bulldog', 'English Cocker Spaniel', 'English Springer Spaniel',
        'French Bulldog', 'German Shepherd', 'German Shorthaired Pointer', 'Golden Retriever', 'Great Dane',
        'Greyhound', 'Jack Russell Terrier', 'Labrador Retriever', 'Maltese', 'Mastiff', 'Newfoundland',
        'Pekingese', 'Pomeranian', 'Poodle', 'Pug', 'Rottweiler', 'Saint Bernard', 'Samoyed',
        'Schnauzer', 'Shetland Sheepdog', 'Shiba Inu', 'Shih Tzu', 'Siberian Husky', 'Yorkshire Terrier',
        'Mixed Breed', 'Other'
    ],
    cat: [
        'Abyssinian', 'American Bobtail', 'American Curl', 'American Shorthair', 'American Wirehair',
        'Balinese', 'Bengal', 'Birman', 'Bombay', 'British Shorthair', 'Burmese', 'Burmilla',
        'Chartreux', 'Cornish Rex', 'Devon Rex', 'Egyptian Mau', 'Exotic Shorthair', 'Havana Brown',
        'Himalayan', 'Japanese Bobtail', 'Javanese', 'Korat', 'LaPerm', 'Maine Coon', 'Manx',
        'Munchkin', 'Nebelung', 'Norwegian Forest Cat', 'Ocicat', 'Oriental', 'Persian', 'Peterbald',
        'Pixie-bob', 'Ragdoll', 'Russian Blue', 'Savannah', 'Scottish Fold', 'Selkirk Rex', 'Siamese',
        'Siberian', 'Singapura', 'Snowshoe', 'Somali', 'Sphynx', 'Tonkinese', 'Turkish Angora',
        'Turkish Van', 'Mixed Breed', 'Other'
    ],
    bird: [
        'African Grey Parrot', 'Amazon Parrot', 'Budgerigar (Budgie)', 'Cockatiel', 'Cockatoo',
        'Conure', 'Eclectus Parrot', 'Finch', 'Lovebird', 'Macaw', 'Parakeet', 'Parrotlet',
        'Pionus Parrot', 'Quaker Parrot', 'Senegal Parrot', 'Canary', 'Dove', 'Pigeon',
        'Chicken', 'Duck', 'Goose', 'Turkey', 'Other'
    ],
    reptile: [
        'Bearded Dragon', 'Blue Tongue Skink', 'Chameleon', 'Crested Gecko', 'Green Iguana',
        'Leopard Gecko', 'Monitor Lizard', 'Python', 'Snake', 'Tortoise', 'Turtle',
        'Other'
    ],
    amphibian: [
        'African Clawed Frog', 'American Toad', 'Axolotl', 'Fire-bellied Toad', 'Green Tree Frog',
        'Pacman Frog', 'Red-eyed Tree Frog', 'Salamander', 'Tiger Salamander', 'Other'
    ],
    rodent: [
        'Chinchilla', 'Degus', 'Dwarf Hamster', 'Ferret', 'Gerbil', 'Guinea Pig',
        'Hamster', 'Mouse', 'Rat', 'Syrian Hamster', 'Other'
    ],
    rabbit: [
        'American Fuzzy Lop', 'American Sable', 'Angora', 'Belgian Hare', 'Britannia Petite',
        'Champagne D\'Argent', 'Checkered Giant', 'Chinchilla', 'Dutch', 'Dwarf Hotot',
        'English Angora', 'English Lop', 'English Spot', 'Flemish Giant', 'Florida White',
        'French Angora', 'French Lop', 'Harlequin', 'Havana', 'Himalayan', 'Holland Lop',
        'Jersey Wooly', 'Lilac', 'Lionhead', 'Mini Lop', 'Mini Rex', 'Netherland Dwarf',
        'New Zealand', 'Polish', 'Rex', 'Rhinelander', 'Satin', 'Silver', 'Silver Fox',
        'Silver Marten', 'Tan', 'Other'
    ],
    fish: [
        'Angelfish', 'Betta', 'Cichlid', 'Clownfish', 'Cory Catfish', 'Danio',
        'Discus', 'Gourami', 'Guppy', 'Koi', 'Molly', 'Neon Tetra', 'Oscar',
        'Platy', 'Plecostomus', 'Rainbowfish', 'Swordtail', 'Tetra', 'Other'
    ],
    invertebrate: [
        'Ant', 'Beetle', 'Crab', 'Hermit Crab', 'Mantis', 'Millipede',
        'Scorpion', 'Shrimp', 'Snail', 'Spider', 'Tarantula', 'Other'
    ],
    other: [
        'Exotic', 'Farm Animal', 'Wild Animal', 'Other'
    ]
};

export const colors = [
    'Black', 'White', 'Brown', 'Grey', 'Red', 'Blue', 'Green', 'Yellow',
    'Orange', 'Purple', 'Pink', 'Tan', 'Cream', 'Gold', 'Silver', 'Multi-colored',
    'Other'
];

export const sexOptions = [
    { label: 'Male', value: 'M' },
    { label: 'Female', value: 'F' },
    { label: 'Unknown', value: 'U' }
]; 
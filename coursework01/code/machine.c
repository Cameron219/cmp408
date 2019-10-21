#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <stdlib.h>

#include "machine.h"

struct machine machines[MAX_MACHINE_COUNT];
int machine_count = 0;

/**
 * Initialize the machine index to -1
 */
void init_machines() {
    for(int i = 0; i < MAX_MACHINE_COUNT; i++) {
        machines[i].index = -1;
    }
}

/**
 * Remove any excess characters from stdin
 */
void flush_stdin() {
    char c;
    while((c = getchar()) != '\n' && c != EOF);
}

/**
 * Prompt user to press enter before returning to main menu
 */
void return_to_menu() {
    printf("\nPress ENTER to return to menu\n");
    getchar();
}

/**
 * Add a machine.
 * Prompts user for the name, location and pin #
 * 
 */
void add_machine() {
    // Let the user know the maximum number of machines has been added
    if(machine_count == MAX_MACHINE_COUNT) {
        printf("Unable to add a new machine.\nThis prototype only allows %d machines at one time. This limit has been reached\n", MAX_MACHINE_COUNT);
    } else {
        struct machine m;

        printf("Add Machine\n* = Character Max\n\n");

        // Index is iterator
        // Previous Index is previously found machines set index
        // Allows new machine to have the correct index
        int index = -1;
        int previous_index = -1;
        for(int i = 0; i < MAX_MACHINE_COUNT; i++) {
            if(machines[i].index == -1) {
                index = i;
                break;
            } else {
                previous_index = machines[i].index;
            }
        }

        // Confirm that there is a blank machine to be added to. This should be handled with the previous if statement as well
        if(index > -1) {
            // Set the index as +1 of previous machine
            m.index = previous_index > -1 ? previous_index + 1 : 1;

            //Prompt user for machine name, and store it
            printf("Enter Machine Name (15*): ");
            scanf(" %15[^\n]%*c", m.name);

            // Prompt user for machine location, and store it
            printf("\nEnter Location (31*): ");
            scanf(" %31[^\n]%*c", m.location);

            // Prompt user for machine pin, and store it
            printf("\nEnter GPIO Pin # (2*): ");
            scanf("%d", &m.pin);
            flush_stdin();

            // Default status of pin is 0
            m.status = 0;

            // Add machine to array
            // Increment machine count
            machines[index] = m;
            machine_count++;
            printf("\nMachine Added!\n");
        } else {
            printf("Error adding machine\n");
        }
    }

    return_to_menu();

}

/**
 * Show all vending machines
 * Displays Index, Name, Pin, Pin Status & Location
 */
void show_all_machines() {
    if(machine_count == 0) {
        printf("No Vending Machines to display\n");
    } else {
        printf("Index\tName\tPin\tStatus\tLocation\n\n");
        for(int i = 0; i < MAX_MACHINE_COUNT; i++) {
            struct machine m = machines[i];
            if(m.index != -1) {
                printf("%d\t%s\t%d\t%d\t%s\n", m.index, m.name, m.pin, m.status, m.location);                
            }
        }
    }

    return_to_menu();
}

/**
 * Search vending machines by their index
 * Display vending machine info if found
 */
void search_by_index() {
    int index = -1;

    printf("Search By Index\n\n");
    printf("Enter Index #: ");
    scanf("%d", &index);
    flush_stdin();

    if(index > -1 && index <= MAX_MACHINE_COUNT) {
        for(int i = 0; i < MAX_MACHINE_COUNT; i++) {
            struct machine m = machines[i];
            if(m.index == index) {
                printf("Index\tName\tPin\tStatus\tLocation\n\n");
                printf("%d\t%s\t%d\t%d\t%s\n", m.index, m.name, m.pin, m.status, m.location);
                return_to_menu();
                return;
            }
        }
    }

    printf("No Machines found\n");
    return_to_menu();
}

/**
 * Delete vending machine data if found
 */
void delete_machine() {
    int index = -1;

    printf("Delete Machine\n\n");
    printf("Enter Index #: ");

    // Prompt user for machine index
    scanf("%d", &index);
    flush_stdin();

    // If index is in valid range
    if(index > 0 && index <= MAX_MACHINE_COUNT) {     
        for(int i = 0; i < MAX_MACHINE_COUNT; i++) {
            if(machines[i].index == index) {
                machines[i].index = -1;
                machine_count--;
                printf("Deleted Machine\n");
            }
        }
    } else {
        printf("Invalid Index\n");
    }

    return_to_menu();
}

/**
 * Update status of specified machine, if found
 */
void update_status() {
    int index = -1;
    int status = -1;
    
    // Prompt user for index of machine
    printf("Update Status\n\n");
    printf("Enter Index #: ");

    scanf("%d", &index);
    flush_stdin();

    // If valid machine
    if(index > 0 && index <= MAX_MACHINE_COUNT) {
        struct machine m;
        int found = -1;
        for(int i = 0; i < MAX_MACHINE_COUNT; i++) {
            m = machines[i];
            // If machine found
            if(m.index == index) {
                found = 1;
                // Prompt user for status
                printf("Enter Status (0/1): ");
                scanf("%d", &status);
                flush_stdin();

                // If status is valid (0,1)
                if(status == 0 || status == 1) {
                    machines[i].status = status;
                    printf("Status Updated\n");
                } else {
                    printf("Invalid Status\n");
                    return_to_menu();
                    return;
                }
            }
        }
        if(found == -1) {
            printf("Machine does not exist\n");
        }
    } else {
        printf("Invalid Index\n");
    }

    return_to_menu();
}

/**
 * Write the struct array of machines to the disk as a binary file
 */
void write_to_file() {
    // Remove old file
    system("rm machines.bin");

    // Create blank file, ready for writing
    system("touch machines.bin");

    FILE *f;
    f = fopen("machines.bin", "a");

    // Write each machine to file
    for(int i = 0; i < MAX_MACHINE_COUNT; i++) {
        struct machine m = machines[i];
        
        if(m.index > 0 && m.index <= MAX_MACHINE_COUNT){
            fwrite(&m, sizeof(m), 1, f);
        }
    }

    fclose(f);
}

/**
 * Read binary file from disk to struct array
 */
void read_from_file() {
    FILE *f;
    struct machine m;
    if((f = fopen("machines.bin", "ab+"))) {
        //printf("File Exists");
        while(fread(&m, sizeof(m), 1, f) > 0) {
            machines[m.index-1] = m;
            machine_count++;
        }
    } else {
        printf("File does not exist");
    }

    fclose(f);
}
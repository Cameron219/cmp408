#include <stdio.h>
#include "machine.h"

struct machine machines[MAX_MACHINE_COUNT];
int machine_count = 0;

void flush_stdin() {
    char c;
    while((c = getchar()) != '\n' && c != EOF);
}

void return_to_menu() {
    printf("\nPress ENTER to return to menu");
    getchar();
}

void add_machine() {
    if(machine_count == MAX_MACHINE_COUNT) {
        printf("Unable to add a new machine.\nThis prototype only allows %d machines at one time. This limit has been reached\n", MAX_MACHINE_COUNT);
    } else {
        struct machine m;

        printf("Add Machine\n* = Character Max\n\n");

        m.index = machine_count + 1;

        printf("Enter Machine Name (15*): ");
        scanf(" %15[^\n]%*c", m.name);

        printf("\nEnter Location (31*): ");
        scanf(" %31[^\n]%*c", m.location);

        printf("\nEnter GPIO Pin # (2*): ");
        scanf("%d", &m.pin);
        flush_stdin();

        m.status = 0;

        machines[machine_count] = m;
        machine_count++;
        printf("\nMachine Added!\n");
    }

    return_to_menu();
}

void show_all_machines() {
    if(machine_count == 0) {
        printf("No Vending Machines to display\n");
    } else {
        printf("Index\tName\t\tPin\tStatus\tLocation\n\n");
        for(int i = 0; i < machine_count; i++) {
            struct machine m = machines[i];
            printf("%d\t%s\t\t%d\t%d\t%s\n", m.index, m.name, m.pin, m.status, m.location);
        }
    }
    
    return_to_menu();
}

void search_by_index() {
    int index;

    printf("Search By Index\n\n");
    printf("Enter index #: ");
    scanf("%d", &index);]
    flush_stdin();
    return_to_menu();
}

void delete_machine() {
    printf("TODO\n");
    return_to_menu();
}

void update_status() {
    printf("TODO\n");
    return_to_menu();
}
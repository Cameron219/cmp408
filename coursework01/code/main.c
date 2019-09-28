#include <stdio.h>
#include <stdlib.h>
#include "menu.h"
#include "machine.h"

void exit_program() {
    printf("Exiting Program\n");
    exit(0);
}

void handle_menu_choice(int choice) {
    // printf("Menu Choice %d\n", choice);
    printf("\n");

    switch(choice) {
        case 1: add_machine(); break;
        case 2: show_all_machines(); break;
        case 3: search_by_index(); break;
        case 4: delete_machine(); break;
        case 5: update_status(); break;
        case 9: exit_program(); break;
    }
}

int main() 
{
    int menu_choice;
    
    while(1) {
        system("clear");
        print_menu();
        menu_choice = get_menu_choice();
        handle_menu_choice(menu_choice);
    }

    return 0;
}
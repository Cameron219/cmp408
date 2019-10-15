#include <stdio.h>
#include <stdlib.h>
#include "menu.h"
#include "machine.h"

/**
 * Exit the program
 * Write machines struct to file
 */
void exit_program() {
    printf("Exiting Program\n");
    write_to_file();
    exit(0);
}

/**
 * Call appropriate function based on user choice
 */
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

/**
 * Main method
 * Initializes machines & shows menu
 */
int main() 
{
    int menu_choice;
    init_machines();
    read_from_file();
    //getchar();
    
    // Continually printing the menu, and prompting the user until the user explicitly exits.
    while(1) {
        system("clear");
        print_menu();
        menu_choice = get_menu_choice();
        handle_menu_choice(menu_choice);
    }

    return 0;
}
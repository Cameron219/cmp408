#include <stdio.h>
#include <stdbool.h>

#define STAR_PADDING 49

void print_star_padding() {
    for(int i = 0; i < STAR_PADDING; i++) {
        printf("*");
    }
    printf("\n");
}

void print_menu()
{
    print_star_padding();

    printf("* Vending Machine Control Console\t\t*\n");
    printf("*\t\tSubmitted By: Cameron McCallion\t*\n");
    printf("*\t\tStudent ID: 1702519\t\t*\n");
    printf("*\t\tAbertay University\t\t*\n");

    print_star_padding();

    printf("1. Add Machine\n");
    printf("2. Show All Machines\n");
    printf("3. Search By Index\n");
    printf("4. Delete Machine\n");
    printf("5. Update Status\n");
    printf("9. Exit\n");

    print_star_padding();
}

bool isValidMenuChoice(int choice) {
    if((choice > 0 && choice < 6) || choice == 9) 
        return true;
    return false;
}

int get_menu_choice()
{
    char c;
    int choice = -1;

    do {
        printf("Enter your choice: ");
        scanf("%c", &c);
        if(c == EOF) return -1;
        if(c == '\n') continue; // Handles user not entering anything (just enter)
        if(getchar() != '\n') { // If there is not any other characters entered.
            while((c = getchar()) != '\n' && c != EOF); // Clear them out until reaches new line or EOF.
        } else {
            choice = c - '0';
        }
        
    } while(!isValidMenuChoice(choice));

    return choice;
}
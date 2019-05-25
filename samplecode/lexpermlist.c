/* lexpermlist.c:
 *
 * This C program obtains a non-negative number n from its user and then
 * lists all permutations of the first n positive integers in increasing
 * lexicographic order. The algorithm was developed by the author of this
 * program by making extensive use of pen and paper to consider the entire
 * set of scenarios for low values of n and then coding solution so observed.
 *
 * Copyright (c) Neil Zanella.
 */

#include <stdio.h>

#define N 20 /* maximum length of an n-tuple */
#define TRUE 1
#define FALSE 0

typedef int Bool;

int a[N + 1], n;

void step_in(void);
void next_permutation(const int left_index, const int right_index);
void print_permutation(void);
void get_n(void);
void initialize_a(void);

main()
{
   get_n();
   initialize_a();

   step_in();

   return 0;
}

/**********************************************************
 * step_in: this recursive function manipulates the       *
 *          values of left_index, right_index, and        *
 *          direction. These variables control the        *
 *          general flow of the algorithm, determining    *
 *          whether a permutation is to be computed or    *
 *          printed and whether the function is to be     *
 *          exited.                                       *
 **********************************************************/

void step_in(void)
{
   static enum {LEFT, RIGHT} direction = RIGHT;
   static left_index = 0;
   int right_index;   /* successive calls of step_in hide */
                      /* previous values of right_index   */

   for (;;) {
      if (direction == RIGHT) {
         ++left_index;
         right_index = n;
         if (left_index == n - 1) {
            print_permutation();
            next_permutation(left_index, right_index);
            print_permutation();
            direction = LEFT;
            break;
         }
         else if (left_index < n - 1)
            step_in();
         else {
            /* special cases where n equals 0 or 1 are treated here */
            print_permutation();
            break;
         }
      }
      else {
         --left_index;
         if (left_index < right_index) {
            next_permutation(left_index, right_index);
            --right_index;
            direction = RIGHT;
            step_in();
         }
         else
            break;
      }
   }
}

/**********************************************************
 * next_permutation: permutes the elements of the array a *
 *                   amongst themselves to obtain a new   *
 *                   permutation of these elements.       *
 **********************************************************/

void next_permutation(const int left_index, const int right_index)
{
   int i;
   int temp[N + 1];
   int top = left_index;

   temp[top] = a[right_index];
   ++top;

   for (i = n; i > right_index; top++, i--)
      temp[top] = a[i];

   temp[top] = a[left_index];
   ++top;

   for (i = right_index - 1; i > left_index; top++, i--)
      temp[top] = a[i];

   for (i = left_index; i <= n; i++)
      a[i] = temp[i];
}

/**********************************************************
 * print_permutation: prints current permutation.         *
 **********************************************************/

void print_permutation(void)
{
   int i;

   printf("\n(");
   if (n > 0) {
      printf(" %2d", a[1]);
      for (i = 2; i <= n; i++)
         printf(", %2d", a[i]);
   }
   printf(" )\n\n");
}

/**********************************************************
 * get_n: prompt user for permutation length n. Read all  *
 *        characters entered by user up to the newline    * 
 *        character. Repeat if either invalid data or an  *
 *        illegal permutation length is entered.          *
 **********************************************************/

void get_n(void)
{
   Bool invalid_int_val;
   char next_char;

   for (;;) {
      invalid_int_val = FALSE;
      printf("\n");
      printf("Enter n: ");
      scanf("%d", &n);
      for(;;) {
         if ((next_char = getchar()) == '\n')
            break;
         else if (next_char != ' ' && next_char != '\t') {
            invalid_int_val = TRUE;
            for(; getchar() != '\n';)
               ;
            break;
         }
      }
      if (invalid_int_val || n < 0 || n > N) {
         printf("\n");
         printf("The number n must be an integer between %d and %d. Try again.\n", 0, N);
      }
      else
         break;
   }
}

/**********************************************************
 * initialize_a: initializes array a.                     *
 **********************************************************/

void initialize_a(void)
{
   int i;

   for (i = 1; i <= n; i++)
      a[i] = i;
}
